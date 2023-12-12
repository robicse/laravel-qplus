<?php

namespace App\Http\Controllers\Common;

use DataTables;
use App\Models\PaymentReceipt;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use App\Http\Traits\CurrencyTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerReturnDueController extends Controller
{  use CurrencyTrait;
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

        //  $this->middleware('permission:customer-due-list', ['only' => ['index', 'show']]);
        //  $this->middleware('permission:customer-due-create', ['only' => ['create', 'store']]);
        //  $this->middleware('permission:customer-due-edit', ['only' => ['edit', 'update']]);
        //  $this->middleware('permission:customer-due-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {

         try {
            if ($request->ajax()) {
                $User=$this->User;
                if ($User->user_type == 'Super Admin') {
                    $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->whereorder_type('Sale Return')->whereorder_type_id(2)->orderBy('id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->wherestore_id($User->store_id)->whereorder_type('Sale Return')->whereorder_type_id(2)->orderBy('id', 'DESC');
                }else{
                    $paymentReceipts = PaymentReceipt::with('store','customer','payment_type')->wherecreated_by_user_id($User->id)->whereorder_type('Sale Return')->whereorder_type_id(2)->orderBy('id', 'DESC');
                }

            return Datatables::of($paymentReceipts)
                ->addIndexColumn()
                ->addColumn('created_by_user', function ($data) {
                    return $data->created_by_user->name;
                })
                ->make(true);
            }
            return view('backend.common.customer_return_dues.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info=Sale::find($id);
        if(($info->grand_total)>($info->paid)){
            return response()->json(['success'=>true,
            'saleinfo'=>$info,
            'message'=>'Due Amount '.$info->due.' '.$this->getCurrencyInfoByDefaultCurrency()->symbol,
        ],200);
        }
        else{
            return response()->json(['success'=>false,
            'message'=>'Alredy Paid'],404);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required',
            'sale_id' => 'required',
            'payment_id' => 'required',
            'paid_amount.*' => 'required|numeric|min:0|max:9999999999999999',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }
        try {
        DB::beginTransaction();
        $SaleInfo = Sale::find($id);
        $voucher_type_id = VoucherType::where('name', 'Received')->pluck('id')->first();
        $get_voucher_code = ChartOfAccountTransaction::orderBy('id', 'desc')->pluck('voucher_no')->first();
        if (!empty($get_voucher_code)) {
            $get_voucher_code_after_replace = str_replace("VOU-", "", $get_voucher_code);
            $voucher_code = (int)$get_voucher_code_after_replace + 1;
        } else {
            $voucher_code = 1;
        }
        $final_voucher_code = 'VOU-' . $voucher_code;

        $date = $SaleInfo->date;
        $van_id = $SaleInfo->van_id?:NULL;
        $route_id = $SaleInfo->route_id?:NULL;
        $transaction_date_time = $SaleInfo->date . ' ' . date('H:i:s');
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        $login_user_id = Auth::user()->id;
        $customer_user_id = $SaleInfo->customer_user_id;
        $payment_type_id = $request->payment_id;
        $amount = $request->paid_amount;

        $final_voucher_no = $final_voucher_code;
        //fetch payment type name
        $paymentTypeName = getPaymentTypeById($payment_type_id);
        $warehouse_id = User::where('id', '=', $customer_user_id)->pluck('warehouse_id')->first();
        if (empty($warehouse_id)) {
            Toastr::error('Sorry not payment completed, please assign a Customer', "Error");
            return back();
        }
        $get_invoice_no = ChartOfAccountTransaction::orderBy('id', 'desc')
            ->where('invoice_no', 'like', 'CUSR-%')
            ->pluck('invoice_no')->first();
        if (!empty($get_invoice_no)) {
            $get_invoice_no_after_replace = str_replace("CUSR-", "", $get_invoice_no);
            $invoice_no = (int)$get_invoice_no_after_replace + 1;
        } else {
            $invoice_no = 1;
        }
        $final_invoice_no = 'CUSR-' . $invoice_no;

        $customerInfo = User::with('customer')->find($customer_user_id);
        $customer_vat_no = $customerInfo->customer->vat_no;
        // Account Receivable Account Info entry
        $customer_chart_of_account_info = WarehouseChartOfAccount::wherewarehouse_id($warehouse_id)->where('user_id', $customer_user_id)->where('parent_head_name', 'ACCOUNT RECEIVABLES')->first();
        if (empty($customer_chart_of_account_info)) {
            DB::rollBack();
            Toastr::error('Sorry Chart Of Account Not Ready To Create Customer', "Error");
            return back();
        }

        if ($this->User->user_type === 'Accounts') {
            $approved_status = 'Approved';
        } else {
            $approved_status = 'Pending';
        }

        if ($paymentTypeName === 'Cash') {

            $cash_in_hand_chart_of_account_info = WarehouseChartOfAccount::where('warehouse_id', $warehouse_id)->where('head_name', 'CASH IN HAND')->first();
            if (empty($cash_in_hand_chart_of_account_info)) {
                DB::rollBack();
                Toastr::error('Sorry Chart Of Account Not Ready To Create CASH IN HAND', "Error");
                return back();
            }

            $description = $customer_chart_of_account_info->head_name . 'Customer receive  On Credit for sales';
            newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Customer Receive', $date, $transaction_date_time, $year, $month, $warehouse_id, $van_id, $route_id, $customer_user_id, NULL, $payment_type_id, NULL, NULL, $customer_chart_of_account_info->id, $customer_chart_of_account_info->head_code, $customer_chart_of_account_info->parent_head_name, $customer_chart_of_account_info->head_type, 0, $amount,  $description, $approved_status,NULL,$customer_vat_no);

            $description = $request->note ? $request->note : 'Warehouse Receive Cash Form Customer On Sales';
            newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Customer Receive', $date, $transaction_date_time, $year, $month, $warehouse_id, $van_id, $route_id, NULL, NULL, $payment_type_id, NULL, NULL, $cash_in_hand_chart_of_account_info->id, $cash_in_hand_chart_of_account_info->head_code, $cash_in_hand_chart_of_account_info->parent_head_name, $cash_in_hand_chart_of_account_info->head_type, $amount, 0,  $description, $approved_status,NULL,$customer_vat_no);
        }

        if ($paymentTypeName === 'Bank') {
            $description = $customer_chart_of_account_info->head_name . 'Customer receive  On Credit for sales';
            newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Customer Receive', $date, $transaction_date_time, $year, $month, $warehouse_id, $van_id, $route_id, $customer_user_id, NULL, $payment_type_id, NULL, NULL, $customer_chart_of_account_info->id, $customer_chart_of_account_info->head_code, $customer_chart_of_account_info->parent_head_name, $customer_chart_of_account_info->head_type, 0, $amount, $description, $approved_status,NULL,$customer_vat_no);

            $bank_chart_of_account_info = WarehouseChartOfAccount::where('warehouse_id', $warehouse_id)->where('prefix', $request->code)->first();
            if (empty($bank_chart_of_account_info)) {
                DB::rollBack();
                Toastr::error('Sorry Chart Of Account Not Ready To Create CASH IN Bank', "Error");
                return back();
            }

            $description = $request->note ? $request->note : 'Form Bank Balance Payment';
            newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Customer Receive', $date, $transaction_date_time, $year, $month, $warehouse_id, $van_id, $route_id, NULL, NULL, $payment_type_id, NULL, NULL, $bank_chart_of_account_info->id, $bank_chart_of_account_info->head_code, $bank_chart_of_account_info->parent_head_name, $bank_chart_of_account_info->head_type, $amount, 0, $description, $approved_status,NULL,$customer_vat_no);



        }
        $SaleInfo->paid = $SaleInfo->paid + $amount;
        $SaleInfo->due = $SaleInfo->due - $amount;
        $SaleInfo->save();
        DB::commit();
        return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return response()->json(['success' => false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
