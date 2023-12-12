<?php

namespace App\Http\Controllers\Common;

use App\Models\Customer;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\PaymentReceipt;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ErrorTryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Models\Sale;
use App\Models\SaleReturn;
use DataTables;
use NumberFormatter;
use App\Helpers\Helper;
use App\Http\Resources\SaleDueCollection;
use App\Http\Traits\BusinessSettingTrait;
use Illuminate\Support\Facades\Redirect;

class CustomerReceiptController extends Controller
{
    use BusinessSettingTrait;
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->User = Auth::user();
            if ($this->User->status == 0) {
                $request->session()->flush();
                return redirect('login');
            }
            return $next($request);
        });
        // $this->middleware('permission:customer-receipts-list', [
        //     'only' => ['index', 'show'],
        // ]);
        // $this->middleware('permission:customer-receipts-create', [
        //     'only' => ['create', 'store'],
        // ]);
        // $this->middleware('permission:customer-receipts-edit', [
        //     'only' => ['edit', 'update'],
        // ]);
        // $this->middleware('permission:customer-receipts-delete', [
        //     'only' => ['destroy'],
        // ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $User=$this->User;
            // $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')
            // ->where('payment_type_id','!=','NULL')
            // ->whereorder_type_id(1)
            // ->where(function ($query) {
            //     $query->whereorder_type('Sale')
            //         ->orWhere('order_type', 'Previous Due');
            // })
            // ->orderBy('id', 'DESC');

            // $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')
            // ->where('payment_receipts.payment_type_id','!=','NULL')
            // ->where('payment_receipts.order_type_id',1)
            // ->where(function ($query) {
            //     $query->where('payment_receipts.order_type','Sale')
            //         ->orWhere('payment_receipts.order_type', 'Previous Due');
            // })
            // ->select('payment_receipts.invoice_no','payment_receipts.created_by_user_id','payment_receipts.date','payment_receipts.store_id','payment_receipts.customer_id',DB::raw('SUM(amount) as amount'))
            // ->groupBy('payment_receipts.invoice_no','payment_receipts.created_by_user_id','payment_receipts.date','payment_receipts.store_id','payment_receipts.customer_id')
            // ->orderBy('payment_receipts.id', 'DESC');
            if ($User->user_type == 'Super Admin') {
                $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->where('order_type','Received')
            ->orderBy('payment_receipts.id', 'DESC');
            }else if ($User->user_type == 'Admin') {
                $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->wherestore_id($User->store_id)->where('order_type','Received')
            ->orderBy('payment_receipts.id', 'DESC');
            }else{
                $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->wherecreated_by_user_id($User->id)->where('order_type','Received')
            ->orderBy('payment_receipts.id', 'DESC');
            }


            return Datatables::of($paymentReceipts)
                ->addIndexColumn()
                // ->addColumn('action', function ($data) {
                //     $btn =
                //         '<span  class="d-inline-flex"><a href=' .
                //         route(
                //             \Request::segment(1) . '.customer-receipts.show',
                //             $data->id
                //         ) .
                //         ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                //     $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/customer-receipts-invoice-pdf/" . $data->id) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a></span>';
                //     return $btn;
                // })
                ->addColumn('action', function ($data) {
                    $btn = '<span  class="d-inline-flex"><a target="_blank" href=' . url(\Request::segment(1) . '/customer-receipts-prints/' . $data->invoice_no . '/a4') . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fa fa-print"></i></a>';
                    $btn .= '<a href=' . route(\Request::segment(1) . '.customer-receipts.edit', $data->id) . ' class="btn btn-info waves-effect btn-sm float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                    $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/customer-receipts-invoice-pdf/" . $data->invoice_no) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a></span>';
                    return $btn;
                })
                ->addColumn('created_by_user', function ($data) {
                    return $data->created_by_user->name;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.common.customer_receipts.index');
    }

    public function create()
    {
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $customers = Customer::wherestatus(1)->get();

        $payment_types = PaymentType::wherestatus(1)->pluck('name', 'id');
        return view('backend.common.customer_receipts.create')
            ->with('customers', $customers)
            ->with('paymentTypes', $payment_types)
            ->with('stores', $stores);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'date' => 'required',
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:0|max:9999999999999999',
        ]);

         // Operation Log initialize
         $activity_id = NULL;
         $status = 'Failed';
         $current_data = [];
         foreach($request->all() as $column => $value){
             $nested_data[$column] = $value;
         }
         array_push($current_data,$nested_data);
         $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();
            $store_id = $request->store_id;
            $date = $request->date;
            $transaction_date_time = $request->date . ' ' . date('H:i:s');
            $login_user_id = Auth::user()->id;
            $customer_id = $request->customer_id;
            $payment_type_id = $request->payment_type_id;
            $total_due = $request->total_due;
            $amount = $request->amount;

            $order_id = '';
            $order_type = 'Received';

            $get_invoice_no = PaymentReceipt::orderBy('id', 'desc')->pluck('invoice_no')->first();
            if ($get_invoice_no) {
                $invoice_no = (int) $get_invoice_no + 1;
            } else {
                $invoice_no = 1;
            }

            // for paid amount > 0
            $payment_receipt = new PaymentReceipt();
            $payment_receipt->invoice_no = $invoice_no;
            $payment_receipt->date = $date;
            $payment_receipt->store_id = $store_id;
            $payment_receipt->order_type = $order_type;
            $payment_receipt->order_id = $order_id;
            $payment_receipt->customer_id = $customer_id;
            $payment_receipt->order_type_id = 1;
            $payment_receipt->payment_type_id = $payment_type_id;
            $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
            $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $payment_receipt->note = $request->note ? $request->note : '';
            //$payment_receipt->total = 0;
            $payment_receipt->amount = $amount;
            $payment_receipt->due = $total_due - $amount;
            $payment_receipt->created_by_user_id = Auth::User()->id;
            $payment_receipt->save();

            // Operation Log Success
            $activity_id = $payment_receipt->id;
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Customer Receipt','Create',NULL,$current_data,$status,$activity_id,NULL);


            DB::commit();
            Toastr::success('Customer Receive  Create Successfully', 'Success');
            // return redirect()->route(\Request::segment(1) . '.customer-receipts.index');
            return Redirect::to(\Request::segment(1) . '/customer-receipts-prints/' . $invoice_no . '/a4');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Customer Receipt','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function store_1(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'date' => 'required',
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:0|max:9999999999999999',
            // 'paid_amount.*' => 'required|numeric|min:0|max:9999999999999999',
        ]);
        try {
            DB::beginTransaction();
            $row_count = count($request->paid_amount);

            $store_id = $request->store_id;
            $date = $request->date;
            $transaction_date_time = $request->date . ' ' . date('H:i:s');
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            $login_user_id = Auth::user()->id;
            $customer_id = $request->customer_id;
            $payment_type_id = $request->payment_type_id;
            $amount = $request->amount;

            for ($i = 0; $i < $row_count; $i++) {
                $due_amount = $request->due_amount[$i];
                $paid_amount = $request->paid_amount[$i];
                $invoice_no = $request->invoice_no[$i];
                $payment_receipt_id = $request->payment_receipt_id[$i];

                if($paid_amount > 0){
                    $order_id = '';
                    $paymentReceipt = PaymentReceipt::findOrFail($payment_receipt_id);
                    $order_type = $paymentReceipt->order_type;
                    if($invoice_no){
                        // $saleInfo = Sale::where('id','=',$invoice_no)->first();
                        // $saleInfo->paid_amount = $saleInfo->paid_amount + $paid_amount;
                        // $saleInfo->due_amount = $saleInfo->due_amount - $paid_amount;
                        // $saleInfo->save();

                        $order_id = $invoice_no;
                    }

                    PaymentReceipt::whereid($payment_receipt_id)->whereorder_type_id(2)->delete();

                    $get_invoice_no = PaymentReceipt::orderBy('id', 'desc')->pluck('invoice_no')->first();
                    if ($get_invoice_no) {
                        $invoice_no = (int) $get_invoice_no + 1;
                    } else {
                        $invoice_no = 1;
                    }

                    // for paid amount > 0
                    $payment_receipt = new PaymentReceipt();
                    $payment_receipt->invoice_no = $invoice_no;
                    $payment_receipt->date = $date;
                    $payment_receipt->store_id = $store_id;
                    $payment_receipt->order_type = $order_type;
                    $payment_receipt->order_id = $order_id;
                    $payment_receipt->customer_id = $customer_id;
                    $payment_receipt->order_type_id = 1;
                    $payment_receipt->payment_type_id = $payment_type_id;
                    $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                    $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                    $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                    $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                    $payment_receipt->note = $request->note ? $request->note : '';
                    $payment_receipt->amount = $paid_amount;
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();

                    // for due amount > 0
                    // if($due_amount > $paid_amount){
                    //     $payment_receipt = new PaymentReceipt();
                    //     $payment_receipt->invoice_no = $invoice_no;
                    //     $payment_receipt->date = $date;
                    //     $payment_receipt->store_id = $store_id;
                    //     $payment_receipt->order_type = $order_type;
                    //     $payment_receipt->order_id = $order_id;
                    //     $payment_receipt->customer_id = $customer_id;
                    //     $payment_receipt->order_type_id = 2;
                    //     $payment_receipt->amount = $due_amount - $paid_amount;
                    //     $payment_receipt->created_by_user_id = Auth::User()->id;
                    //     $payment_receipt->save();
                    // }
                }
            }

            DB::commit();
            Toastr::success('Customer Receive  Create Successfully', 'Success');
            // return redirect()->route(\Request::segment(1) . '.customer-receipts.index');
            return Redirect::to(\Request::segment(1) . '/customer-receipts-prints/' . $invoice_no . '/a4');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function customerDue($id)
    {
        return PaymentReceipt::wherecustomer_id($id)->whereorder_type_id(2)->sum('amount');
    }
    public function CustomerBanks($id)
    {
        //
    }

    public function customerDueBalanceInfo($id)
    {

        // $sales = Sale::where('customer_id', $id)->where('due_amount', '!=', 0)->select('id', 'due_amount')->get();
        // $sale_dues = [];
        // if(count($sales) > 0){

        //     foreach($sales as $data){
        //         $sale_due_amount = $data->due_amount;
        //         $sale_return_invoice_due = SaleReturn::where('sale_id','=',$data->id)->sum('invoice_due');
        //         $sale_return_refundable_amount = SaleReturn::where('sale_id','=',$data->id)->sum('refundable_amount');
        //         $sale_return_refund_amount = SaleReturn::where('sale_id','=',$data->id)->sum('refund_amount');
        //         $sale_return_due_amount = SaleReturn::where('sale_id','=',$data->id)->sum('due_amount');
        //         $sale_return_due = SaleReturn::where('sale_id','=',$data->id)->sum('due_amount');
        //         $due_amount = $sale_due_amount - $sale_return_invoice_due;
        //         if($due_amount > 0){
        //             $nested_data['id'] = $data->id;
        //             $nested_data['due_amount'] = $data->due_amount;

        //             array_push($sale_dues,$nested_data);
        //         }

        //     }
        // }

        $dues = PaymentReceipt::where('customer_id', $id)->where('order_type_id', 2)->select('id', 'order_id','amount')->get();
        $due_data = [];
        if(count($dues) > 0){

            foreach($dues as $data){
                $due_amount = $data->amount;
                $order_id = $data->order_id != NULL ? $data->order_id : '';
                if($due_amount > 0){
                    $nested_data['payment_receipt_id'] = $data->id;
                    $nested_data['order_id'] = $order_id;
                    $nested_data['due_amount'] = $due_amount;

                    array_push($due_data,$nested_data);
                }

            }
        }

        return $due_data;

        // return new SaleDueCollection(Sale::where('customer_id', $id)->where('due_amount', '!=', 0)->select('id', 'due_amount')->get());
    }

    public function customerDueAmount($customer_id)
    {
        $store_id = 1;
        return Helper::customerDueAmount($store_id,$customer_id);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $customers = Customer::wherestatus(1)->get();
        $payment_types = PaymentType::wherestatus(1)->pluck('name', 'id');
        $paymentReceipt = PaymentReceipt::where('id', $id)->where('order_type', 'Received')->first();
        return view('backend.common.customer_receipts.edit')
            ->with('customers', $customers)
            ->with('paymentTypes', $payment_types)
            ->with('paymentReceipt', $paymentReceipt)
            ->with('stores', $stores);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $this->validate($request, [
            'date' => 'required',
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:0|max:9999999999999999',
        ]);

        // Operation Log initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = PaymentReceipt::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);

        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();
            $store_id = $request->store_id;
            $date = $request->date;
            $transaction_date_time = $request->date . ' ' . date('H:i:s');
            $login_user_id = Auth::user()->id;
            $customer_id = $request->customer_id;
            $payment_type_id = $request->payment_type_id;
            $due = $request->due;
            $amount = $request->amount;

            $payment_receipt = PaymentReceipt::findOrFail($id);
            $previous_due = $payment_receipt->due;
            $previous_amount = $payment_receipt->amount;
            $current_due = 0;
            if($previous_amount > $amount){
                $balance_amount = $previous_amount - $amount;
                if($previous_due > 0){
                    $current_due = $previous_due + $balance_amount;
                }
            }else{
                $balance_amount = $amount - $previous_amount;
                if($previous_due > 0){
                    $current_due = $previous_due - $balance_amount;
                }
            }
            $payment_receipt->date = $date;
            $payment_receipt->store_id = $store_id;
            $payment_receipt->customer_id = $customer_id;
            $payment_receipt->payment_type_id = $payment_type_id;
            $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
            $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $payment_receipt->note = $request->note ? $request->note : '';
            $payment_receipt->amount = $amount;
            $payment_receipt->due = $current_due;
            $payment_receipt->updated_by_user_id = Auth::User()->id;
            $payment_receipt->save();

            // Operation Log Success
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Customer Receipt','Update',$previous_data,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success('Customer Receive  Update Successfully', 'Success');
            // return Redirect::to(\Request::segment(1) . '/customer-receipts-prints/' . $id . '/a4');
            return Redirect::to(\Request::segment(1) . '/customer-receipts');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Customer Receipt','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function customerReceiptsPrintWithPageSize($id, $pagesize)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $transactions = PaymentReceipt::where('invoice_no',$id)->where('order_type','Received')->get();
        $created_by_user_id = $transactions[0]->created_by_user_id;
        return view('backend.common.customer_receipts.print_with_size', compact('digit','default_business_settings','pagesize','transactions','created_by_user_id'));
    }

    public function customerReceiptsInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $transactions = PaymentReceipt::where('invoice_no',$id)->where('order_type','Received')->get();
        $created_by_user_id = $transactions[0]->created_by_user_id;
        $pdf = Pdf::loadView('backend.common.customer_receipts.invoice_pdf', compact('digit','default_business_settings','transactions','created_by_user_id'));
        return $pdf->stream('customer_receipts_invoice_' . now() . '.pdf');
    }
}
