<?php

namespace App\Http\Controllers\Common;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Models\PaymentType;
use App\Models\PaymentReceipt;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Helper;
use NumberFormatter;
use App\Http\Traits\BusinessSettingTrait;
use Illuminate\Support\Facades\Redirect;

class SupplierPaymentController extends Controller
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
        $this->middleware('permission:supplier-payments-list', ['only' => ['index', 'show']]);
        // $this->middleware('permission:supplier-payments-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:supplier-payments-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:supplier-payments-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        try {

            if ($request->ajax()) {

                // $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')->whereorder_type('Purchase')->whereorder_type_id(1)->orderBy('id', 'DESC');

                // $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')
                // ->where('payment_receipts.payment_type_id','!=','NULL')
                // ->where('payment_receipts.order_type_id',1)
                // ->where(function ($query) {
                //     $query->where('payment_receipts.order_type','Purchase');
                // })
                // ->select('payment_receipts.invoice_no','payment_receipts.created_by_user_id','payment_receipts.date','payment_receipts.store_id','payment_receipts.supplier_id',DB::raw('SUM(amount) as amount'))
                // ->groupBy('payment_receipts.invoice_no','payment_receipts.created_by_user_id','payment_receipts.date','payment_receipts.store_id','payment_receipts.supplier_id')
                // ->orderBy('payment_receipts.id', 'DESC');



                if ($User->user_type == 'Super Admin') {
                    $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')->where('receipt_time','Paid Amount')->orderBy('payment_receipts.id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')->wherestore_id($User->store_id)->where('receipt_time','Paid Amount')->orderBy('payment_receipts.id', 'DESC');
                }else{
                    $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')->wherecreated_by_user_id($User->id)->where('receipt_time','Paid Amount')->orderBy('payment_receipts.id', 'DESC');
                }

                return Datatables::of($paymentReceipts)
                    ->addIndexColumn()
                    ->addColumn('action', function ($data) {
                        // $btn =
                        //     '<span  class="d-inline-flex"><a href=' .
                        //     route(
                        //         \Request::segment(1) . '.supplier-payments.show',
                        //         $data->invoice_no
                        //     ) .
                        //     ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                        $btn = '<span  class="d-inline-flex"><a target="_blank" href=' . url(\Request::segment(1) . '/supplier-payments-prints/' . $data->invoice_no . '/a4') . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fa fa-print"></i></a>';
                        $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/supplier-payments-invoice-pdf/" . $data->invoice_no) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a></span>';
                        return $btn;
                    })
                    ->addColumn('created_by_user', function ($data) {
                        return $data->created_by_user->name;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('backend.common.supplier_payments.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        //dd('ff');
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $suppliers = Supplier::wherestatus(1)->get();

        $payment_types = PaymentType::wherestatus(1)->pluck('name', 'id');
        return view('backend.common.supplier_payments.create')
            ->with('suppliers', $suppliers)
            ->with('paymentTypes', $payment_types)
            ->with('stores', $stores);

    }
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'date' => 'required',
            'supplier_id' => 'required',
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
            $supplier_id = $request->supplier_id;
            $payment_type_id = $request->payment_type_id;
            $total_due = $request->total_due;
            $amount = $request->amount;

            $order_id = '';
            $order_type = 'Paid Amount';

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
            $payment_receipt->supplier_id = $supplier_id;
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
            $payment_receipt->receipt_time = 'Paid Amount';
            $payment_receipt->created_by_user_id = Auth::User()->id;
            $payment_receipt->save();


            // Operation Log Success
            $activity_id = $payment_receipt->id;
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Supplier Payment','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success('Customer Receive  Create Successfully', 'Success');
            // return redirect()->route(\Request::segment(1) . '.customer-receipts.index');
            return Redirect::to(\Request::segment(1) . '/supplier-payments-prints/' . $invoice_no . '/a4');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Supplier Payment','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function store_1(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'supplier_id' => 'required',
            'amount' => 'required|numeric|min:0|max:9999999999999999',
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
            $supplier_id = $request->supplier_id;
            $payment_type_id = $request->payment_type_id;
            $amount = $request->amount;

            for ($i = 0; $i < $row_count; $i++) {
                $due_amount = $request->due_amount[$i];
                $paid_amount = $request->paid_amount[$i];
                $invoice_no = $request->invoice_no[$i];
                $payment_receipt_id = $request->payment_receipt_id[$i];

                // for paid amount > 0
                if($paid_amount > 0){
                    $order_id = '';
                    $paymentReceipt = PaymentReceipt::findOrFail($payment_receipt_id);
                    $order_type = $paymentReceipt->order_type;
                    if($invoice_no){
                        $purchaseInfo = Purchase::where('id','=',$invoice_no)->first();
                        $purchaseInfo->paid_amount = $purchaseInfo->paid_amount + $paid_amount;
                        $purchaseInfo->due_amount = $purchaseInfo->due_amount - $paid_amount;
                        $purchaseInfo->save();

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
                    $payment_receipt->date = date('Y-m-d');
                    $payment_receipt->store_id = $store_id;
                    $payment_receipt->order_type = $order_type;
                    $payment_receipt->order_id = $order_id;
                    $payment_receipt->supplier_id = $supplier_id;
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
                    if($due_amount > $paid_amount){
                        $payment_receipt = new PaymentReceipt();
                        $payment_receipt->invoice_no = $invoice_no;
                        $payment_receipt->date = date('Y-m-d');
                        $payment_receipt->store_id = $store_id;
                        $payment_receipt->order_type = $order_type;
                        $payment_receipt->order_id = $order_id;
                        $payment_receipt->supplier_id = $supplier_id;
                        $payment_receipt->order_type_id = 2;
                        $payment_receipt->amount = $due_amount - $paid_amount;
                        $payment_receipt->created_by_user_id = Auth::User()->id;
                        $payment_receipt->save();
                    }
                }
            }

            DB::commit();
            Toastr::success('Customer Receive  Create Successfully', 'Success');
            // return redirect()->route(\Request::segment(1) . '.supplier-payments.index');
            return Redirect::to(\Request::segment(1) . '/customer-receipts-prints/' . $invoice_no . '/a4');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function show($id)
    {
        //
    }

    public function supplierDueBalanceInfo($id)
    {
        // return Purchase::where('supplier_id', $id)->where('due_amount', '!=', 0)->select('id', 'due_amount')->get();
        $dues = PaymentReceipt::where('supplier_id', $id)->where('order_type_id', 2)->select('id', 'order_id','amount')->get();
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
    }

    public function supplierDueAmount($supplier_id)
    {
        $store_id = 1;
        return Helper::supplierDueAmount($store_id,$supplier_id);
    }

    public function supplierPaymentsPrintWithPageSize($id, $pagesize)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $transactions = PaymentReceipt::where('invoice_no',$id)->get();
        $created_by_user_id = $transactions[0]->created_by_user_id;
        return view('backend.common.supplier_payments.print_with_size', compact('digit','default_business_settings','pagesize','transactions','created_by_user_id'));
    }

    public function supplierPaymentsInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $transactions = PaymentReceipt::where('invoice_no',$id)->get();
        $created_by_user_id = $transactions[0]->created_by_user_id;
        $pdf = Pdf::loadView('backend.common.supplier_payments.invoice_pdf', compact('digit','default_business_settings','transactions','created_by_user_id'));
        return $pdf->stream('customer_receipts_invoice_' . now() . '.pdf');
    }
}
