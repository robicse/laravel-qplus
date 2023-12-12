<?php
namespace App\Http\Controllers\Common;
use DataTables;
use App\Models\PaymentReceipt;
use App\Models\Sale;
use App\Models\User;
use App\Models\Receive;
use App\Models\PaymentType;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use App\Http\Traits\CurrencyTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\WarehouseChartOfAccount;
use App\Models\ChartOfAccountTransaction;
use Illuminate\Support\Facades\Validator;

class SupplierDueController extends Controller
{
    use CurrencyTrait;
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

        //  $this->middleware('permission:supplier-dues-list', ['only' => ['index', 'show']]);
        //  $this->middleware('permission:supplier-dues-create', ['only' => ['create', 'store']]);
        //  $this->middleware('permission:supplier-dues-edit', ['only' => ['edit', 'update']]);
        //  $this->middleware('permission:supplier-dues-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {

        try {
            if ($request->ajax()) {


                if ($User->user_type == 'Super Admin') {
                    $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')
                    ->whereorder_type_id(2)
                    ->where('supplier_id', '!=', null)
                    ->where(function ($query) {
                        $query
                            ->whereorder_type('Purchase')
                            ->orWhere('order_type', 'Previous Due');
                    })
                    ->orderBy('id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')
                    ->wherestore_id($User->store_id)
                    ->whereorder_type_id(2)
                    ->where('supplier_id', '!=', null)
                    ->where(function ($query) {
                        $query
                            ->whereorder_type('Purchase')
                            ->orWhere('order_type', 'Previous Due');
                    })
                    ->orderBy('id', 'DESC');
                }else{
                    $paymentReceipts = PaymentReceipt::with('store','supplier','payment_type')
                    ->wherecreated_by_user_id($User->id)
                    ->whereorder_type_id(2)
                    ->where('supplier_id', '!=', null)
                    ->where(function ($query) {
                        $query
                            ->whereorder_type('Purchase')
                            ->orWhere('order_type', 'Previous Due');
                    })
                    ->orderBy('id', 'DESC');
                }

            return Datatables::of($paymentReceipts)
                ->addIndexColumn()
                ->addColumn('created_by_user', function ($data) {
                    return $data->created_by_user->name;
                })
                ->make(true);
            }


            return view('backend.common.supplier_dues.index');
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
        $info = Receive::find($id);
        if (($info->grand_total) > ($info->paid)) {
            return response()->json([
                'success' => true,
                'receiveinfo' => $info,
                'message' => 'Due Amount ' . $info->due . ' ' . $this->getCurrencyInfoByDefaultCurrency()->symbol,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Alredy Paid'
            ], 404);
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
            'receive_id' => 'required',
            'payment_id' => 'required',
            'amount.*' => 'required|numeric|min:0|max:9999999999999999',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }

        $amount = $request->amount;
        $warehouse = $request->warehouse_id;
        $bank = $request->code;
        $info = getCashBalance($warehouse, $bank);
        if ($info['debit_credit'] == 'Credit') {
            return response()->json([
                'success' => false,
                'errors' => array(0 => 'Sorry You Can Not Payment for Credit Balance Amount Please Add Debit Balance')
            ]);
        }
        try {
            DB::beginTransaction();
            $ReceiveInfo = Receive::find($id);
            $voucher_type_id = VoucherType::where('name', 'Payment')->pluck('id')->first();
            $get_voucher_code = ChartOfAccountTransaction::orderBy('id', 'desc')->pluck('voucher_no')->first();
            if (!empty($get_voucher_code)) {
                $get_voucher_code_after_replace = str_replace("SUPPAY-", "", $get_voucher_code);
                $voucher_code = (int)$get_voucher_code_after_replace + 1;
            } else {
                $voucher_code = 1;
            }
            $final_voucher_code = 'SUPPAY-' . $voucher_code;
            $date = $ReceiveInfo->date;
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            $login_user_id = Auth::user()->id;
            $supplier_user_id = $ReceiveInfo->supplier_user_id;
            $payment_type_id = $request->payment_id;
            $transaction_date_time = $date . date(' H:i:s');
            $final_voucher_no = $final_voucher_code;
            //fetch payment type name
            $paymentTypeName = getPaymentTypeById($payment_type_id);
            $warehouse_id = User::where('id', '=', $supplier_user_id)->pluck('warehouse_id')->first();
            if (empty($warehouse_id)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => array(0 => 'Sorry not payment completed, please assign a supplier warehouse')
                ]);

                return back();
            }
            if ($info['amount'] < $amount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => array(0 => 'Sorry You Can Not Payment  Greater Than Debit Balance')
                ]);

                return back();
            }



            $get_invoice_no = ChartOfAccountTransaction::orderBy('id', 'desc')
                ->where('invoice_no', 'like', 'SUPP-%')
                ->pluck('invoice_no')->first();
            if (!empty($get_invoice_no)) {
                $get_invoice_no_after_replace = str_replace("SUPP-", "", $get_invoice_no);
                $invoice_no = (int)$get_invoice_no_after_replace + 1;
            } else {
                $invoice_no = 1;
            }
            $final_invoice_no = 'SUPP-' . $invoice_no;



            // Account Payable Account Info entry
            $supplier_chart_of_account_info = WarehouseChartOfAccount::wherewarehouse_id($warehouse_id)->where('user_id', $supplier_user_id)->where('parent_head_name', 'ACCOUNT PAYABLES')->first();
            if (empty($supplier_chart_of_account_info)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => array(0 => 'Sorry Chart Of Account Not Ready To Create Suppplier')
                ]);

                return back();
            }

            if ($this->User->user_type === 'Accounts') {
                $approved_status = 'Approved';
            } else {
                $approved_status = 'Pending';
            }
            $SupplierInfo = User::with('supplier')->find($supplier_user_id);
            $supplier_vat_no = $SupplierInfo->supplier->vat_no;
            if ($paymentTypeName === 'Cash') {
                $description = $supplier_chart_of_account_info->head_name . ' Supplier payment';
                newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Supplier Payment', $date, $transaction_date_time, $year, $month, $warehouse_id, NULL, NULL, $supplier_user_id, NULL, $payment_type_id, NULL, NULL, $supplier_chart_of_account_info->id, $supplier_chart_of_account_info->head_code, $supplier_chart_of_account_info->parent_head_name, $supplier_chart_of_account_info->head_type, $amount, 0, $description, $approved_status,$supplier_vat_no,NULL);
                $cash_in_hand_chart_of_account_info = WarehouseChartOfAccount::where('warehouse_id', $warehouse_id)->where('head_name', 'CASH IN HAND')->first();
                if (empty($cash_in_hand_chart_of_account_info)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'errors' => array(0 => 'Sorry Chart Of Account Not Ready To Create CASH IN HAND')
                    ]);

                    return back();
                }

                $description = $request->note ? $request->note : 'Cash Payment';
                newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Supplier Payment', $date, $transaction_date_time, $year, $month, $warehouse_id, NULL, NULL, NULL, NULL, $payment_type_id, NULL, NULL, $cash_in_hand_chart_of_account_info->id, $cash_in_hand_chart_of_account_info->head_code, $cash_in_hand_chart_of_account_info->parent_head_name, $cash_in_hand_chart_of_account_info->head_type, 0, $amount, $description, $approved_status,$supplier_vat_no,NULL);
            }

            if ($paymentTypeName === 'Bank') {

                $description = $supplier_chart_of_account_info->head_name . ' Supplier payment  debit for Bank';
                newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Supplier Payment', $date, $transaction_date_time, $year, $month, $warehouse_id, NULL, NULL, $supplier_user_id, NULL, $payment_type_id, NULL, NULL, $supplier_chart_of_account_info->id, $supplier_chart_of_account_info->head_code, $supplier_chart_of_account_info->parent_head_name, $supplier_chart_of_account_info->head_type, $amount, 0, $description, $approved_status,$supplier_vat_no,NULL);
                $bank_chart_of_account_info = WarehouseChartOfAccount::where('warehouse_id', $warehouse_id)->where('prefix', $request->code)->first();
                if (empty($bank_chart_of_account_info)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'errors' => array(0 => 'Sorry Chart Of Account Not Ready To Create CASH IN Bank')
                    ]);

                    return back();
                }

                $description = $request->note ? $request->note : 'Form Bank Balance Payment';
                newChartOfAccountTransactions(NULL, $final_invoice_no, $login_user_id, $voucher_type_id, $final_voucher_no, 'Supplier Payment', $date, $transaction_date_time, $year, $month, $warehouse_id, NULL, NULL, NULL, NULL, $payment_type_id, NULL, NULL, $bank_chart_of_account_info->id, $bank_chart_of_account_info->head_code, $bank_chart_of_account_info->parent_head_name, $bank_chart_of_account_info->head_type, 0, $amount, $description, $approved_status,$supplier_vat_no,NULL);
            }



            if ($amount !== NUll) {

                $ReceiveInfo->paid = $ReceiveInfo->paid + $amount;
                $ReceiveInfo->due = $ReceiveInfo->due - $amount;
                $ReceiveInfo->save();
            }

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
