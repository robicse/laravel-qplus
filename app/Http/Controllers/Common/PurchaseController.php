<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\Helper;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderType;
use App\Models\PaymentType;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\PaymentReceipt;
use App\Http\Traits\CurrencyTrait;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Traits\BusinessSettingTrait;
use DataTables;
use NumberFormatter;

class PurchaseController extends Controller
{
    use CurrencyTrait;
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
        $this->middleware('permission:purchases-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchases-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchases-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchases-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        try {
            $User=$this->User;
            if ($request->ajax()) {
                if ($User->user_type == 'Super Admin') {
                    $purchases = Purchase::with('store','supplier')->latest();
                }else if ($User->user_type == 'Admin') {
                    $purchases = Purchase::wherestore_id($User->store_id)->orderBy('id', 'DESC');
                }else{
                    $purchases = Purchase::wherecreated_by_user_id($User->id)->orderBy('id', 'DESC');
                }

                return Datatables::of($purchases)
                    ->addIndexColumn()
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'purchases\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'purchases\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($purchase)use($User) {
                        $btn='';
                        $btn .= '<span  class="d-inline-flex"><a href=' . route(\Request::segment(1) . '.purchases.show', $purchase->id) . ' class="btn btn-sm btn-warning waves-effect float-left" style="margin-left: 5px"><i class="fa fa-eye"></i></a>';
                        // if($User->can('purchases-edit')){
                        $btn .= '<a href=' . route(\Request::segment(1) . '.purchases.edit', $purchase->id) . ' class="btn btn-info btn-sm waves-effect float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                        // }
                        // $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . '/purchases-prints/' . $purchase->id . '/a4') . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fa fa-print"></i></a>';
                        $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/purchases-invoice-pdf/" . $purchase->id) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a>';
                        $btn .= '<form method="post" action=' . route(\Request::segment(1) . '.purchases.destroy',$purchase->id) . '>'.csrf_field().'<input type="hidden" name="_method" value="DELETE">';
                        $btn .= '<button class="btn btn-sm btn-danger" style="margin-left: 5px;" type="submit" onclick="return confirm(\'You Are Sure This Delete !\')"><i class="fa fa-trash"></i></button>';
                        $btn .= '</form></span>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }

            return view('backend.common.purchases.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        $User=$this->User;
        $order_types = OrderType::whereIn('name', ['Cash', 'Credit'])->get();
        $cash_payment_types = PaymentType::whereIn('name', ['Cash', 'Card', 'Online'])->get();
        $credit_payment_types = PaymentType::whereIn('name', ['Cheque', 'Condition'])->get();
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $suppliers = Supplier::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        $units = Unit::wherestatus(1)->get();
        return view('backend.common.purchases.create', compact('stores','suppliers','categories','order_types','cash_payment_types', 'credit_payment_types','units'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'purchase_date' => 'required',
            'store_id' => 'required',
            'supplier_id' => 'required',
            'total_qty' => 'required',
            'sub_total' => 'required',
            'grand_total' => 'required',
            'total_sale_price' => 'required',
            //'discount_amount' => 'required',
            'paid' => 'required',
            'product_category_id.*' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required',
            'purchase_price.*' => 'required',
            'sale_price.*' => 'required'
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
            $purchase_date = $request->purchase_date;
            $store_id = $request->store_id;
            $supplier_id = $request->supplier_id;
            $total_qty = $request->total_qty;
            $sub_total = $request->sub_total;
            $discount_amount = $request->discount ? $request->discount : 0;
            $grand_total = $request->grand_total;
            $total_sale_price = $request->total_sale_price;
            $payment_type_id = $request->payment_type_id;
            $paid_amount = $request->paid;
            $due_amount = $request->due;
            $discount_type = 'Flat';
            $discount_percent = NULL;
            $discount = 0;
            $total_vat = $request->total_vat;
            //$category_id = $request->category_id;
            $unit_id = $request->unit_id;
            $product_id = $request->product_id;
            $qty = $request->qty;
            $purchase_price = $request->purchase_price;
            $sale_price = $request->sale_price;

            $purchase = new Purchase();
            $purchase->purchase_date = $purchase_date;
            $purchase->store_id = $store_id;
            $purchase->supplier_id = $supplier_id;
            $purchase->total_qty = $total_qty;
            $purchase->sub_total = $sub_total;
            $purchase->discount_amount = $discount_amount;
            $purchase->total_vat = $total_vat;
            $purchase->discount_type = $discount_type;
            $purchase->discount_percent = $discount_percent;
            $purchase->after_discount = $grand_total;
            $purchase->grand_total = $grand_total;
            $purchase->payment_type_id = $payment_type_id;
            $purchase->paid_amount = $paid_amount;
            $purchase->due_amount = $due_amount;
            $purchase->total_sale_price = $total_sale_price;
            if($request->payment_type_id != NULL){
                $purchase->payment_type_id =  $request->payment_type_id;
            }
            $purchase->bank_name = $request->bank_name ? $request->bank_name : '';
            $purchase->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $purchase->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $purchase->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $purchase->note = $request->note ? $request->note : '';
            $purchase->status = 1;
            $purchase->created_by_user_id = Auth::User()->id;
            $insert_id = $purchase->save();
            if($insert_id){
                for($i=0; $i<count($product_id); $i++){
                    $product = Product::whereid($product_id[$i])->first();
                    $p_id = $product_id[$i];
                    $p_price = $purchase_price[$i];
                    $p_sale_price = $sale_price[$i];
                    $p_qty = $qty[$i];
                    $total = ($p_qty * $p_price);
                    //change  to unit id
                    $unit_id = $request->unit_id[$i];
                    $product_vat = $request->product_vat[$i];
                    $product_vat_amount = $request->product_vat_amount[$i];
                    $average_purchase_price = 0;
                    //discount calculation
                    $final_discount_amount = 0;

                    $stock = new Stock();
                    $stock->purchase_id = $purchase->id;
                    $stock->store_id = $store_id;
                    //$stock->category_id = $product->category_id;
                    $stock->qty = $p_qty;
                    $stock->product_id = $p_id;
                    $stock->unit_id = $unit_id;
                    $stock->already_return_qty = 0;
                    $stock->purchase_price = $p_price;
                    $stock->sale_price = $p_sale_price;
                    $stock->product_total = $total;
                    $stock->product_vat = $product_vat;
                    $stock->product_vat_amount = $product_vat_amount;
                    $stock->product_total = $total - $final_discount_amount + $product_vat_amount;
                    $stock->product_discount_type = $discount_type;
                    $stock->product_discount_percent = $discount_percent;
                    $stock->product_discount = $final_discount_amount;
                    $stock->after_product_discount = $total - $final_discount_amount + $product_vat_amount;
                    $stock->created_by_user_id = Auth::User()->id;
                    $stock->save();
                }

                $get_invoice_no = PaymentReceipt::orderBy('id', 'desc')->pluck('invoice_no')->first();
                if ($get_invoice_no) {
                    $invoice_no = (int) $get_invoice_no + 1;
                } else {
                    $invoice_no = 1;
                }

                // for paid amount > 0
                // if($paid_amount > 0){
                    $payment_receipt = new PaymentReceipt();
                    $payment_receipt->invoice_no = $invoice_no;
                    $payment_receipt->date = $purchase_date;
                    $payment_receipt->store_id = $store_id;
                    $payment_receipt->order_type = 'Purchase';
                    $payment_receipt->order_id = $purchase->id;
                    $payment_receipt->supplier_id = $supplier_id;
                    $payment_receipt->order_type_id = 1;
                    $payment_receipt->payment_type_id = $request->payment_type_id;
                    $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                    $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                    $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                    $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                    $payment_receipt->note = $request->note ? $request->note : '';
                    $payment_receipt->total = $grand_total;
                    $payment_receipt->amount = $paid_amount;
                    $payment_receipt->due = $due_amount;
                    $payment_receipt->receipt_time = 'Purchase';
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                // }

                // for due amount > 0
                // if($due_amount > 0){
                //     $payment_receipt = new PaymentReceipt();
                //     $payment_receipt->invoice_no = $invoice_no;
                //     $payment_receipt->date = $purchase_date;
                //     $payment_receipt->store_id = $store_id;
                //     $payment_receipt->order_type = 'Purchase';
                //     $payment_receipt->order_id = $purchase->id;
                //     $payment_receipt->supplier_id = $supplier_id;
                //     $payment_receipt->order_type_id = 2;
                //     $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                //     $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                //     $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                //     $payment_receipt->amount = $due_amount;
                //     $payment_receipt->receipt_time = 'Purchase';
                //     $payment_receipt->created_by_user_id = Auth::User()->id;
                //     $payment_receipt->save();
                // }

                // Operation Log Success
                $activity_id = $purchase->id;
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Purchase','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Purchase Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Purchase','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function show($id)
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $purchase = Purchase::findOrFail($id);
        $supplier = Supplier::findOrFail($purchase->supplier_id);
        $purchaseDetails = Stock::where('purchase_id', $purchase->id)->get();
        return view('backend.common.purchases.show', compact('purchase', 'supplier','purchaseDetails', 'default_currency'));
    }

    public function edit($id)
    {
        $User=$this->User;
        $purchase = Purchase::findOrFail($id);
        $stocks = Stock::wherepurchase_id($id)->get();
        $order_types = OrderType::whereIn('name', ['Cash', 'Credit'])->get();
        $cash_payment_types = PaymentType::whereIn('name', ['Cash', 'Card', 'Online'])->get();
        $credit_payment_types = PaymentType::whereIn('name', ['Cheque', 'Condition'])->get();
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $suppliers = Supplier::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        $units = Unit::wherestatus(1)->get();
        return view('backend.common.purchases.edit', compact('purchase','stocks','stores','suppliers','categories','order_types','cash_payment_types', 'credit_payment_types','units'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $this->validate($request, [
            'purchase_date' => 'required',
            'store_id' => 'required',
            'supplier_id' => 'required',
            'total_qty' => 'required',
            'sub_total' => 'required',
            'grand_total' => 'required',
            'total_sale_price' => 'required',
            //'discount_amount' => 'required',
            'paid' => 'required',
            'product_category_id.*' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required',
            'purchase_price.*' => 'required',
            'sale_price.*' => 'required'
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = Purchase::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);
        $data2 = Stock::wherepurchase_id($id)->get();
        $decode_data2 = json_decode($data2->toJson());
        array_push($previous_data,$decode_data2);

        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();
            $purchase_date = $request->purchase_date;
            $store_id = $request->store_id;
            $supplier_id = $request->supplier_id;
            $total_qty = $request->total_qty;
            $sub_total = $request->sub_total;
            $discount_amount = $request->discount ? $request->discount : 0;
            $grand_total = $request->grand_total;
            $total_sale_price = $request->total_sale_price;
            $order_type_id = $request->order_type_id;
            $payment_type_id = $request->payment_type_id;
            $paid_amount = $request->paid;
            $due_amount = $request->due;
            $discount_type = 'Flat';
            $discount_percent = NULL;
            $discount = 0;
            $total_vat = $request->total_vat;
            //$category_id = $request->category_id;
            $unit_id = $request->unit_id;
            $product_id = $request->product_id;
            $qty = $request->qty;
            $purchase_price = $request->purchase_price;
            $sale_price = $request->sale_price;

            $purchase = Purchase::findOrFail($id);
            $purchase->purchase_date = $purchase_date;
            $purchase->store_id = $store_id;
            $purchase->supplier_id = $supplier_id;
            $purchase->total_qty = $total_qty;
            $purchase->sub_total = $sub_total;
            $purchase->discount_amount = $discount_amount;
            $purchase->total_vat = $total_vat;
            $purchase->discount_type = $discount_type;
            $purchase->discount_percent = $discount_percent;
            $purchase->after_discount = $grand_total;
            $purchase->grand_total = $grand_total;
            $purchase->order_type_id = $order_type_id;
            if($request->payment_type_id != NULL){
                $purchase->payment_type_id =  $request->payment_type_id;
            }
            $purchase->bank_name = $request->bank_name ? $request->bank_name : '';
            $purchase->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $purchase->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $purchase->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $purchase->note = $request->note ? $request->note : '';
            $purchase->paid_amount = $paid_amount;
            $purchase->due_amount = $due_amount;
            $purchase->total_sale_price = $total_sale_price;
            $purchase->status = 1;
            $purchase->updated_by_user_id = Auth::User()->id;
            if($purchase->save()){
                DB::table('stocks')->where('purchase_id',$id)->delete();
                DB::table('payment_receipts')->where('order_id',$id)->whereorder_type('Purchase')->delete();
                for($i=0; $i<count($product_id); $i++){
                    $product = Product::whereid($product_id[$i])->first();
                    $p_id = $product_id[$i];
                    $u_id = $product->unit_id;
                    $p_price = $purchase_price[$i];
                    $p_sale_price = $sale_price[$i];
                    $p_qty = $qty[$i];
                    $total = ($p_qty * $p_price);
                    //change  to unit id
                    $product_vat = $request->product_vat[$i];
                    $product_vat_amount = $request->product_vat_amount[$i];
                    $average_purchase_price = 0;
                    //discount calculation
                    $final_discount_amount = 0;

                    $stock = new Stock();
                    $stock->purchase_id = $purchase->id;
                    $stock->store_id = $store_id;
                    //$stock->category_id = $product->category_id;
                    $stock->qty = $p_qty;
                    $stock->product_id = $p_id;
                    $stock->unit_id = $u_id;
                    $stock->already_return_qty = 0;
                    $stock->purchase_price = $p_price;
                    $stock->sale_price = $p_sale_price;
                    $stock->product_total = $total;
                    $stock->product_vat = $product_vat;
                    $stock->product_vat_amount = $product_vat_amount;
                    $stock->product_total = $total - $final_discount_amount + $product_vat_amount;
                    $stock->product_discount_type = $discount_type;
                    $stock->product_discount_percent = $discount_percent;
                    $stock->product_discount = $final_discount_amount;
                    $stock->after_product_discount = $total - $final_discount_amount + $product_vat_amount;
                    $stock->created_by_user_id = Auth::User()->id;
                    $stock->save();
                }

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
                    $payment_receipt->date = date('Y-m-d');
                    $payment_receipt->store_id = $store_id;
                    $payment_receipt->order_type = 'Purchase';
                    $payment_receipt->order_id = $purchase->id;
                    $payment_receipt->supplier_id = $supplier_id;
                    $payment_receipt->order_type_id = 1;
                    $payment_receipt->payment_type_id = $request->payment_type_id;
                    $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                    $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                    $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                    $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                    $payment_receipt->note = $request->note ? $request->note : '';
                    $payment_receipt->total = $grand_total;
                    $payment_receipt->amount = $paid_amount;
                    $payment_receipt->due = $due_amount;
                    $payment_receipt->receipt_time = 'Purchase';
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                }

                // for due amount > 0
                // if($due_amount > 0){
                //     $payment_receipt = new PaymentReceipt();
                //     $payment_receipt->invoice_no = $invoice_no;
                //     $payment_receipt->date = date('Y-m-d');
                //     $payment_receipt->store_id = $store_id;
                //     $payment_receipt->order_type = 'Purchase';
                //     $payment_receipt->order_id = $purchase->id;
                //     $payment_receipt->supplier_id = $supplier_id;
                //     $payment_receipt->order_type_id = 2;
                //     $payment_receipt->amount = $due_amount;
                //     $payment_receipt->created_by_user_id = Auth::User()->id;
                //     $payment_receipt->save();
                // }

                // Operation Log Success
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Purchase','Update',$previous_data,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Purchase Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Purchase','Update',$previous_data,$current_data,'Error',$activity_id,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function destroy($id)
    {
        // Operation Log Initialize
        $activity_id = $id;
        $previous_data = [];
        $data = Purchase::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);
        $data2 = Stock::wherepurchase_id($id)->get();
        $decode_data2 = json_decode($data2->toJson());
        array_push($previous_data,$decode_data2);
        $purchase = Purchase::find($id);
        $store_id = $purchase->store_id;

        try {
            DB::beginTransaction();
            DB::table('stocks')->where('purchase_id',$id)->delete();
            DB::table('payment_receipts')->where('order_id',$id)->whereorder_type('Purchase')->delete();
            $purchase->delete();

            // Operation Log Success
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Purchase','Delete',$previous_data,NULL,$status,NULL,NULL);

            DB::commit();
            Toastr::success("SaleReturn Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Purchase','Delete',$previous_data,NULL,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function purchasePrintWithPageSize($id, $pagesize)
    {
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $purchase = Purchase::findOrFail($id);
        $stocks = Stock::where('purchase_id', $id)->get();
        $previousDue= Purchase::where('id','!=',$id)->wheresupplier_id($purchase->supplier_id)->sum('due_amount');
        return view('backend.common.purchases.print_with_size', compact('purchase', 'stocks', 'pagesize','previousDue','default_currency','digit'));
    }

    public function purchaseInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $purchase = Purchase::findOrFail($id);
        $created_by_user_id = $purchase->created_by_user_id;
        $stocks = Stock::where('purchase_id', $id)->get();
        $previousDue= Purchase::where('id','!=',$id)->wheresupplier_id($purchase->supplier_id)->sum('due_amount');
        $transactions = PaymentReceipt::where('order_id',$id)->where('order_type','Purchase')->where('payment_type_id','!=',NULL)->get();
        // $pdf = Pdf::loadView('backend.common.report.purchase_details_pdf', compact('purchase', 'stocks','previousDue'));
        $pdf = Pdf::loadView('backend.common.purchases.invoice_pdf', compact('purchase', 'stocks','previousDue','digit','default_business_settings','transactions','created_by_user_id'));
        return $pdf->stream('purchase_invoice_' . now() . '.pdf');
    }

    public function FindProductBySearchProductName(Request $request)
    {
        if ($request->has('q')) {
            $data = Product::where('status', 1)
                ->where('name', 'like', '%' . $request->q . '%')
                ->select('id', 'name')->get();
            if ($data) {
                return response()->json($data);
            } else {
                return response()->json(['success' => false, 'product' => 'Error!!']);
            }
        }
    }

    public function categoryProductInfo(Request $request)
    {
        $options = [
            'productOptions' => '',
        ];
        $category_id = $request->current_category_id;
        $products = Product::wherestatus(1)->wherecategory_id($category_id)->get();
        if (count($products) > 0) {
            $options['productOptions'] .= "<option value=''>Select Product</option>";
            foreach ($products as $key => $product) {
                $options['productOptions'] .= "<option value='$product->id'>" . $product->name . "</option>";
            }
        } else {
            $options['productOptions'] .= "<option value=''>No Data Found!</option>";
        }

        return response()->json(['success' => true, 'data' => $options]);
    }
}
