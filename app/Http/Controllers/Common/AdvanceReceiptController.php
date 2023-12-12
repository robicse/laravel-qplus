<?php

namespace App\Http\Controllers\Common;

use App\Models\Customer;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ErrorTryCatch;
use App\Models\AdvanceReceipt;
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

class AdvanceReceiptController extends Controller
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
        $User=$this->User;
        if ($request->ajax()) {
            if ($User->user_type == 'Super Admin') {
                $advanceReceipts = AdvanceReceipt::with('store','customer','payment_type')->orderBy('id', 'DESC');
            }else if ($User->user_type == 'Admin') {
                $advanceReceipts = AdvanceReceipt::with('store','customer','payment_type')->wherestore_id($User->store_id)->orderBy('id', 'DESC');
            }else{
                $advanceReceipts = AdvanceReceipt::with('store','customer','payment_type')->wherecreated_by_user_id($User->id)->orderBy('id', 'DESC');
            }

            return Datatables::of($advanceReceipts)
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
                    $btn = '<span  class="d-inline-flex"><a target="_blank" href=' . url(\Request::segment(1) . "/advance-receipts-invoice-pdf/" . $data->id) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a>';
                    $btn .= '<a href=' . route(\Request::segment(1) . '.advance-receipts.edit', $data->id) . ' class="btn btn-info btn-sm waves-effect float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a></span>';
                    return $btn;
                })
                ->addColumn('created_by_user', function ($data) {
                    return $data->created_by_user->name;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.common.advance_receipts.index');
    }

    public function create()
    {
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        $customers = Customer::wherestatus(1)->get();

        $payment_types = PaymentType::wherestatus(1)->pluck('name', 'id');
        return view('backend.common.advance_receipts.create')
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
            'amount' => 'required|numeric|min:0|max:9999999999999999'
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
            $date = $request->date;
            $store_id = $request->store_id;
            $customer_id = $request->customer_id;
            $payment_type_id = $request->payment_type_id;
            $amount = $request->amount;

            // for paid amount > 0
            $advance_receipt = new AdvanceReceipt();
            $advance_receipt->date = $date;
            $advance_receipt->store_id = $store_id;
            $advance_receipt->customer_id = $customer_id;
            $advance_receipt->type = 'Advance';
            $advance_receipt->payment_type_id = $payment_type_id;
            $advance_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
            $advance_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $advance_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $advance_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $advance_receipt->note = $request->note ? $request->note : '';
            $advance_receipt->amount = $amount;
            $advance_receipt->created_by_user_id = Auth::User()->id;
            $advance_receipt->save();

            // Operation Log Success
            $activity_id = $advance_receipt->id;
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Advance Receipt','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success('Advance Receive  Create Successfully', 'Success');
            return redirect()->route(
                \Request::segment(1) . '.advance-receipts.index'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Advance Receipt','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $advance_receipt = AdvanceReceipt::findOrFail($id);
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        $customers = Customer::wherestatus(1)->get();
        $paymentTypes = PaymentType::wherestatus(1)->pluck('name', 'id');
        $store_id = $advance_receipt->store_id;
        $customer_id = $advance_receipt->customer_id;
        return view('backend.common.advance_receipts.edit', compact('advance_receipt','customers','stores','paymentTypes','store_id', 'customer_id'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $this->validate($request, [
            'date' => 'required',
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:0|max:9999999999999999'
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = AdvanceReceipt::findOrFail($id);
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
            $date = $request->date;
            $store_id = $request->store_id;
            $customer_id = $request->customer_id;
            $payment_type_id = $request->payment_type_id;
            $amount = $request->amount;

            // for paid amount > 0
            $advance_receipt = AdvanceReceipt::find($id);;
            $advance_receipt->date = $date;
            $advance_receipt->store_id = $store_id;
            $advance_receipt->customer_id = $customer_id;
            $advance_receipt->payment_type_id = $payment_type_id;
            $advance_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
            $advance_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $advance_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $advance_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $advance_receipt->note = $request->note ? $request->note : '';
            $advance_receipt->amount = $amount;
            $advance_receipt->created_by_user_id = Auth::User()->id;
            $advance_receipt->save();

            // Operation Log Success
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Advance Receipt','Update',$previous_data,$current_data,$status,$activity_id,NULL);


            DB::commit();
            Toastr::success('Advance Receive  Create Successfully', 'Success');
            return redirect()->route(
                \Request::segment(1) . '.advance-receipts.index'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Advance Receipt','Update',$previous_data,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function advanceReceiptsInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $transaction = AdvanceReceipt::where('id',$id)->first();
        $created_by_user_id = $transaction->created_by_user_id;
        $pdf = Pdf::loadView('backend.common.advance_receipts.invoice_pdf', compact('digit','default_business_settings','transaction','created_by_user_id'));
        return $pdf->stream('advance_receipts_invoice_' . now() . '.pdf');
    }
}
