<?php

namespace App\Http\Controllers\Common;

use App\Models\Customer;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Models\Sale;
use App\Models\SaleReturn;
use DataTables;
use App\Helpers\Helper;

class CustomerPaymentController extends Controller
{
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

            if ($User->user_type == 'Super Admin') {
                $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->whereorder_type('Sale Return')->whereorder_type_id(1)->orderBy('id', 'DESC');
            }else if ($User->user_type == 'Admin') {
                $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->wherestore_id($User->store_id)->whereorder_type('Sale Return')->whereorder_type_id(1)->orderBy('id', 'DESC');
            }else{
                $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->wherecreated_by_user_id($User->id)->whereorder_type('Sale Return')->whereorder_type_id(1)->orderBy('id', 'DESC');
            }

            return Datatables::of($paymentReceipts)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn =
                        '<a href=' .
                        route(
                            \Request::segment(1) . '.customer-payments.show',
                            $data->id
                        ) .
                        ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by_user', function ($data) {
                    return $data->created_by_user->name;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.common.customer_payments.index');
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
        return view('backend.common.customer_payments.create')
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
            // 'paid_amount.*' => 'required|numeric|min:0|max:9999999999999999',
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();
            $row_count = count($request->paid_amount);

            $store_id = $request->store_id;
            $date = $request->date;
            $customer_id = $request->customer_id;
            $payment_type_id = $request->payment_type_id;
            $amount = $request->amount;
            $due_amount = $request->due_amount;

            $get_invoice_no = PaymentReceipt::orderBy('id', 'desc')->pluck('invoice_no')->first();
            if ($get_invoice_no) {
                $invoice_no = (int) $get_invoice_no + 1;
            } else {
                $invoice_no = 1;
            }

            // for paid amount > 0
            if($paid_amount > 0){
                $payment_receipt = new PaymentReceipt();
                $payment_receipt->invoice_no = $invoice_no;
                $payment_receipt->date = $date;
                $payment_receipt->store_id = $store_id;
                $payment_receipt->order_type = 'Sale Return';
                $payment_receipt->order_id = '';
                $payment_receipt->customer_id = $customer_id;
                $payment_receipt->order_type_id = 1;
                $payment_receipt->payment_type_id = $payment_type_id;
                $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                $payment_receipt->note = $request->note ? $request->note : '';
                $payment_receipt->total = 0;
                $payment_receipt->amount = $amount;
                $payment_receipt->due = $due_amount - $amount;
                $payment_receipt->created_by_user_id = Auth::User()->id;
                $payment_receipt->save();
            }

            // Operation Log Success
            $activity_id = $payment_receipt->id;
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Customer Payment','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success('Customer Receive  Create Successfully', 'Success');
            return redirect()->route(
                \Request::segment(1) . '.customer-payments.index'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Customer Payment','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

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

    public function customerReturnDueBalanceInfo($id)
    {
        // return SaleReturn::where('customer_id', $id)->where('due_amount', '!=', 0)->select('id', 'due_amount')->get();
        return SaleReturn::where('customer_id', $id)->where('due_amount', '!=', 0)->sum('due_amount');
    }

    public function show($id)
    {
        //
    }
}
