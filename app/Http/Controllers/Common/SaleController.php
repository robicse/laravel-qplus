<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\Helper;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\CurrencyTrait;
use App\Models\PaymentReceipt;
use App\Models\AdvanceReceipt;
use App\Models\OrderType;
use App\Models\PaymentType;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\SalePackage;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\Product;
use App\Models\Package;
use App\Models\Store;
use App\Models\Customer;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use NumberFormatter;
use App\Http\Traits\BusinessSettingTrait;
use Illuminate\Support\Facades\Redirect;

class SaleController extends Controller
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
        $this->middleware('permission:sales-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:sales-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        /* Custom Update */
        // $sales = Sale::where('order_type_id', 2)->get();
        // if(count($sales) > 0){
        //     foreach($sales as $sale){
        //         $order_id = $sale->id;
        //         $order_type_id = $sale->order_type_id;
        //         if($order_type_id == 2){
        //             $paymentReceipt = PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->first();
        //             if($paymentReceipt){
        //                 echo $paymentReceipt->id.'<br/>';
        //                 $paymentReceipt->order_type_id = 2;
        //                 $paymentReceipt->save();
        //             }
        //         }
        //     }
        // }
        // die();
        /* Custom Update */



        try {
            $User=$this->User;
            if ($request->ajax()) {
                if ($User->user_type == 'Super Admin') {
                    $sales = Sale::with('customer')->orderBy('id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $sales = Sale::with('customer')->wherestore_id($User->store_id)->orderBy('id', 'DESC');
                }else{
                    $sales = Sale::with('customer')->wherecreated_by_user_id($User->id)->orderBy('id', 'DESC');
                }
                return Datatables::of($sales)
                    ->addIndexColumn()
                    ->addColumn('store', function ($data) {
                        return $data->store->name;
                    })
                    // ->addColumn('customer', function ($data) {
                    //     return $data->customer->name;
                    // })
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'sales\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'sales\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($sale)use($User) {
                        $btn='';
                        $btn = '<span  class="d-inline-flex"><a href=' . route(\Request::segment(1) . '.sales.show', $sale->id) . ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                        $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . '/sales-prints/' . $sale->id . '/a4') . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fa fa-print"></i></a>';
                        // $btn .= '<a href=' . url(\Request::segment(1) . "/sales-prints/" . $sale->id . '/80mm') . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fa fa-print"></i>80MM</a>';
                        // $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/sales-invoice-pdf/" . $sale->id) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a>';
                        if($User->can('sales-edit')){
                            $btn .= '<a href=' . route(\Request::segment(1) . '.sales.edit', $sale->id) . ' class="btn btn-info btn-sm waves-effect float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                        }
                        if($User->can('sales-delete')){
                            $btn .= '<form method="post" action=' . route(\Request::segment(1) . '.sales.destroy',$sale->id) . '>'.csrf_field().'<input type="hidden" name="_method" value="DELETE">';
                            $btn .= '<button class="btn btn-sm btn-danger" style="margin-left: 5px;" type="submit" onclick="return confirm(\'You Are Sure This Delete !\')"><i class="fa fa-trash"></i></button>';
                            $btn .= '</form>';
                        }
                        $btn .= '</span>';

                        return $btn;
                    })
                    ->rawColumns(['category','action', 'status'])
                    ->make(true);
            }

            return view('backend.common.sales.index');
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
        $customers = Customer::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        $units = Unit::wherestatus(1)->get();
        $packages = Package::wherestatus(1)->pluck('name','id');
        return view('backend.common.sales.create', compact('stores','customers','categories','units','packages','order_types','cash_payment_types','credit_payment_types'));
    }

    public function customerAdvanceBalanceInfo($id)
    {
        $amount = AdvanceReceipt::where('customer_id', $id)->where('type', 'Advance')->sum('amount');
        $minus_amount = AdvanceReceipt::where('customer_id', $id)->where('type', 'Advance Minus')->sum('amount');
        return $amount - $minus_amount;
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'voucher_date' => 'required',
            'store_id' => 'required',
            'customer_id' => 'required',
            'total_quantity' => 'required',
            'sub_total' => 'required',
            'grand_total' => 'required',
            //'discount' => 'required',
            'paid' => 'required',
            'due' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required',
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
            $voucher_date = $request->voucher_date;
            $store_id = $request->store_id;
            $customer_id = $request->customer_id;
            $order_type_id = $request->sale_type_id;
            $total_quantity = $request->total_quantity;
            $sub_total = $request->sub_total;
            $discount_type = $request->discount_type;
            $discount_percent = $request->discount_percent;
            $discount_amount = $request->discount ? $request->discount : 0;
            $total_vat = $request->total_vat;
            $grand_total = $request->grand_total;
            $after_discount_amount = $grand_total - $discount_amount;
            $paid_amount = $request->paid;
            $due_amount = $request->due;
            $hc_voucher_number = $request->hc_voucher_number;
            $comments = $request->comments;

            $product_id = $request->product_id;
            $unit_id = $request->unit_id;
            $qty = $request->qty;
            $product_vat = $request->product_vat;
            $product_vat_amount = $request->product_vat_amount;
            $sale_price = $request->sale_price;
            $total = $request->total;
            $package_id = $request->package_id;

            $profit_amount = 0;
            for($x=0; $x<count($product_id); $x++){
                $p_id = $product_id[$x];
                $p_qty = $qty[$x];
                $stock_info = Stock::wherestore_id($store_id)->whereproduct_id($p_id)->select('purchase_price','sale_price')->orderBy('id', 'DESC')->first();
                if($stock_info){
                    $per_qty_profit_amount = $stock_info->sale_price - $stock_info->purchase_price;
                    $profit_amount += $per_qty_profit_amount * $p_qty;
                }
            }

            $sale = new Sale();
            $sale->voucher_date = $voucher_date;
            $sale->store_id = $store_id;
            $sale->customer_id = $customer_id;
            $sale->order_type_id = $order_type_id;
            $sale->total_quantity = $total_quantity;
            $sale->sub_total = $sub_total;
            $sale->discount_type = $discount_type;
            $sale->discount_percent = $discount_percent;
            $sale->discount_amount = $discount_amount;
            $sale->total_vat = $total_vat;
            $sale->grand_total = $grand_total;
            $sale->advance_minus_amount = $request->minus_amount;
            $sale->paid_amount = $paid_amount;
            $sale->due_amount = $due_amount;
            $sale->profit_amount = $profit_amount;
            if($request->payment_type_id != NULL){
                $sale->payment_type_id =  $request->payment_type_id;
            }
            $sale->bank_name = $request->bank_name ? $request->bank_name : '';
            $sale->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $sale->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $sale->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $sale->note = $request->note ? $request->note : '';
            $sale->hc_voucher_number = $hc_voucher_number;
            $sale->comments = $comments;
            $sale->status = 1;
            $sale->created_by_user_id = Auth::User()->id;
            $insert_id = $sale->save();
            if($insert_id){
                for($i=0; $i<count($product_id); $i++){
                    $product = Product::whereid($product_id[$i])->first();
                    $p_id = $product_id[$i];
                    $p_qty = $qty[$i];
                    $total_amount = 0;
                    $stock_info = Stock::wherestore_id($store_id)->whereproduct_id($p_id)->select('purchase_price','sale_price')->orderBy('id', 'DESC')->first();
                    if($stock_info){
                        $per_qty_profit_amount = $stock_info->sale_price - $stock_info->purchase_price;
                        $total_amount += $per_qty_profit_amount * $p_qty;
                    }

                    $sub_total = ($qty[$i] * $sale_price[$i]);
                    $unit_id = $request->unit_id[$i] != NULL ? $request->unit_id[$i] : 0;
                    $product_vat = $request->product_vat[$i] != NULL ? $request->product_vat[$i] : 0;
                    $product_vat_amount = $request->product_vat_amount[$i] != NULL ? $request->product_vat_amount[$i] : 0;
                    $producttotal = $total[$i];
                    $final_discount_amount = 0;
                    $per_product_discount = 0;
                    $extra_discount_amount = NULL;




                    if ($discount_type != NULL && $discount_amount > 0) {
                        //$discount_amount = $request->discount;

                        // including vat
                        $cal_discount = $discount_amount;
                        $cal_product_total_amount = $product_vat_amount + $producttotal;
                        $cal_grand_total = $sub_total + $total_vat;

                        // echo '$cal_discount'.$cal_discount;
                        // echo '$cal_product_total_amount'.$cal_product_total_amount;
                        // echo '$cal_grand_total'.$cal_grand_total;
                        // die();

                        $cal_discount_amount =  (round((float)$cal_discount, 2) * round((float)$cal_product_total_amount, 2)) / round((float)$cal_grand_total, 2);
                        $final_discount_amount = round((float)$cal_discount_amount, 2);
                        $per_product_discount =  $final_discount_amount / $qty[$i];
                    }

                    $sale_product = new SaleProduct();
                    $sale_product->sale_id = $sale->id;
                    $sale_product->store_id = $store_id;
                    $sale_product->category_id =$product->category_id;
                    $sale_product->unit_id = $product->unit_id;
                    $sale_product->product_id = $product_id[$i];
                    $sale_product->qty = $qty[$i];
                    $sale_product->sale_price = $sale_price[$i];
                    $sale_product->total = $sub_total;
                    $sale_product->product_vat = $product_vat;
                    $sale_product->product_vat_amount = $product_vat_amount;
                    $sale_product->product_discount_type = $discount_type;
                    $sale_product->per_product_discount = $per_product_discount;
                    $sale_product->product_discount_percent = $discount_percent;
                    $sale_product->product_discount = $final_discount_amount;
                    $sale_product->after_product_discount = ($sub_total + $product_vat_amount) - $final_discount_amount;
                    $sale_product->product_total = ($sub_total + $product_vat_amount) - $final_discount_amount;
                    $sale_product->per_product_profit = $per_qty_profit_amount;
                    $sale_product->total_profit = $total_amount;
                    $sale_product->created_by_user_id = Auth::User()->id;
                    $sale_product->save();
                }
                if($package_id){
                    $sale_package = new SalePackage();
                    $sale_package->sale_id = $sale->id;
                    $sale_package->store_id = $store_id;
                    $sale_package->package_id = $package_id;
                    $sale_package->amount = Package::whereid($package_id)->pluck('amount')->first();
                    $sale_package->created_by_user_id = Auth::User()->id;
                    $sale_package->save();
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
                    $payment_receipt->date = $voucher_date;
                    $payment_receipt->store_id = $store_id;
                    $payment_receipt->order_type = 'Sale';
                    $payment_receipt->order_id = $sale->id;
                    $payment_receipt->customer_id = $customer_id;
                    $payment_receipt->order_type_id = $order_type_id;
                    if($request->payment_type_id != NULL){
                        $payment_receipt->payment_type_id =  $request->payment_type_id;
                    }
                    $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                    $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                    $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                    $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                    $payment_receipt->note = $request->note ? $request->note : '';
                    $payment_receipt->total = $grand_total;
                    $payment_receipt->amount = $paid_amount;
                    $payment_receipt->advance_minus_amount = $request->minus_amount != null ? $request->minus_amount : 0;
                    $payment_receipt->due = $due_amount;
                    $payment_receipt->comments = $comments;
                    $payment_receipt->receipt_time = 'Sale';
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                // }

                // for due amount > 0
                // if($due_amount > 0){
                //     $payment_receipt = new PaymentReceipt();
                //     $payment_receipt->invoice_no = $invoice_no;
                //     $payment_receipt->date = $voucher_date;
                //     $payment_receipt->store_id = $store_id;
                //     $payment_receipt->order_type = 'Sale';
                //     $payment_receipt->order_id = $sale->id;
                //     $payment_receipt->customer_id = $customer_id;
                //     $payment_receipt->order_type_id = 2;
                //     if($request->payment_type_id != NULL){
                //         $payment_receipt->payment_type_id =  $request->payment_type_id;
                //     }
                //     $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                //     $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                //     $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                //     $payment_receipt->amount = $due_amount;
                //     $payment_receipt->comments = $comments;
                //     $payment_receipt->receipt_time = 'Sale';
                //     $payment_receipt->created_by_user_id = Auth::User()->id;
                //     $payment_receipt->save();
                // }

                if($request->paid_from_advance == "on"){
                    $advance_receipt = new AdvanceReceipt();
                    $advance_receipt->date = $voucher_date;
                    if($request->payment_type_id != NULL){
                        $advance_receipt->payment_type_id =  $request->payment_type_id;
                    }else{
                        $advance_receipt->payment_type_id = 1;
                    }
                    $advance_receipt->sale_id = $sale->id;
                    $advance_receipt->store_id = $store_id;
                    $advance_receipt->customer_id = $customer_id;
                    $advance_receipt->type = 'Advance Minus';
                    $advance_receipt->amount = $request->minus_amount;
                    //$advance_receipt->receipt_time = 'Sale';
                    $advance_receipt->created_by_user_id = Auth::User()->id;
                    $advance_receipt->save();
                }

                // Operation Log Success
                $activity_id = $sale->id;
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Sale','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Sale Created Successfully", "Success");
            return Redirect::to(\Request::segment(1) . '/sales-prints/' . $sale->id . '/a4');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Sale','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function show($id)
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $sale = Sale::findOrFail($id);
        $saleDetails = SaleProduct::where('sale_id', $id)->get();
        $previousDue = Sale::where('id', '!=', $id)->wherecustomer_id($sale->customer_id)->sum('due_amount');
        return view('backend.common.sales.show', compact('sale', 'saleDetails', 'default_currency', 'previousDue'));
    }

    public function edit($id)
    {
        $User=$this->User;
        $sale = Sale::with('customer')->findOrFail($id);
        $saleDetails = SaleProduct::where('sale_id', $id)->get();
        $SalePackages = SalePackage::where('sale_id', $id)->get();
        $order_types = OrderType::whereIn('name', ['Cash', 'Credit'])->get();
        $cash_payment_types = PaymentType::whereIn('name', ['Cash', 'Card', 'Online'])->get();
        $credit_payment_types = PaymentType::whereIn('name', ['Cheque', 'Condition'])->get();
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $customers = Customer::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        $units = Unit::wherestatus(1)->get();
        $packages = Package::wherestatus(1)->pluck('name','id');
        $paymentReceipt = PaymentReceipt::where('order_id', $id)->where('order_type', 'Sale')->first();
        return view('backend.common.sales.edit', compact('sale', 'saleDetails', 'SalePackages','stores','customers','categories','units','packages','cash_payment_types','credit_payment_types','order_types','paymentReceipt'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $this->validate($request, [
            'voucher_date' => 'required',
            'store_id' => 'required',
            'customer_id' => 'required',
            'total_quantity' => 'required',
            'sub_total' => 'required',
            'grand_total' => 'required',
            //'discount' => 'required',
            'paid' => 'required',
            'due' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required',
            'sale_price.*' => 'required'
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = Sale::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);
        $data2 = SaleProduct::wheresale_id($id)->get();
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
            $voucher_date = $request->voucher_date;
            $store_id = $request->store_id;
            $customer_id = $request->customer_id;
            $order_type_id = $request->sale_type_id;
            $total_quantity = $request->total_quantity;
            $sub_total = $request->sub_total;
            $discount_type = $request->discount_type;
            $discount_percent = $request->discount_percent;
            $discount_amount = $request->discount ? $request->discount : 0;
            $total_vat = $request->total_vat;
            $grand_total = $request->grand_total;
            $after_discount_amount = $grand_total - $discount_amount;
            $paid_amount = $request->paid;
            $due_amount = $request->due;

            $product_id = $request->product_id;
            $unit_id = $request->unit_id;
            $qty = $request->qty;
            $product_vat = $request->product_vat != NULL ? $request->product_vat : 0;
            $product_vat_amount = $request->product_vat_amount;
            $sale_price = $request->sale_price;
            $total = $request->total;
            $package_id = $request->package_id;

            $profit_amount = 0;
            for($x=0; $x<count($product_id); $x++){
                $p_id = $product_id[$x];
                $p_qty = $qty[$x];
                $stock_info = Stock::wherestore_id($store_id)->whereproduct_id($p_id)->select('purchase_price','sale_price')->orderBy('id', 'DESC')->first();
                if($stock_info){
                    $per_qty_profit_amount = $stock_info->sale_price - $stock_info->purchase_price;
                    $profit_amount += $per_qty_profit_amount * $p_qty;
                }
            }

            $sale = Sale::findOrFail($id);
            $sale->voucher_date = $voucher_date;
            $sale->store_id = $store_id;
            $sale->customer_id = $customer_id;
            $sale->order_type_id = $order_type_id;
            $sale->total_quantity = $total_quantity;
            $sale->sub_total = $sub_total;
            $sale->discount_type = $discount_type;
            $sale->discount_percent = $discount_percent;
            $sale->discount_amount = $discount_amount;
            $sale->total_vat = $total_vat;
            $sale->grand_total = $grand_total;
            $sale->advance_minus_amount = $request->advance_minus_amount;
            $sale->paid_amount = $paid_amount;
            $sale->due_amount = $due_amount;
            $sale->profit_amount = $profit_amount;
            $sale->status = 1;
            $sale->updated_by_user_id = Auth::User()->id;
            $update_sale = $sale->save();
            if($update_sale){
                DB::table('sale_products')->where('sale_id',$id)->delete();
                DB::table('sale_packages')->where('sale_id',$id)->delete();
                DB::table('payment_receipts')->where('order_id',$id)->whereorder_type('Sale')->delete();
                for($i=0; $i<count($product_id); $i++){
                    $product = Product::whereid($product_id[$i])->first();
                    $p_id = $product_id[$i];
                    $p_qty = $qty[$i];
                    $p_sale_price = $sale_price[$i];
                    $p_product_vat = $request->product_vat[$i] != NULL ? $request->product_vat[$i] : 0;
                    $total_amount = 0;
                    $stock_info = Stock::wherestore_id($store_id)->whereproduct_id($p_id)->select('purchase_price','sale_price')->orderBy('id', 'DESC')->first();
                    if($stock_info){
                        $per_qty_profit_amount = $stock_info->sale_price - $stock_info->purchase_price;
                        $total_amount += $per_qty_profit_amount * $p_qty;
                    }

                    $sub_total = ($p_qty * $p_sale_price);
                    $unit_id = $request->unit_id[$i] != NULL ? $request->unit_id[$i] : 0;
                    $product_vat = $p_product_vat != NULL ? $p_product_vat : 0;
                    $product_vat_amount = $request->product_vat_amount[$i] != NULL ? $request->product_vat_amount[$i] : 0;
                    $producttotal = $total[$i];
                    $final_discount_amount = 0;
                    $extra_discount_amount = NULL;
                    if ($discount_type != NULL) {
                        //$discount_amount = $request->discount;

                        // including vat
                        $cal_discount = $discount_amount;
                        $cal_product_total_amount = $product_vat_amount + $producttotal;
                        $cal_grand_total = $sub_total + $total_vat;

                        $cal_discount_amount =  (round((float)$cal_discount, 2) * round((float)$cal_product_total_amount, 2)) / round((float)$cal_grand_total, 2);
                        $final_discount_amount = round((float)$cal_discount_amount, 2);
                        $per_product_discount =  $final_discount_amount / $p_qty;
                    }

                    $sale_product = new SaleProduct();
                    $sale_product->sale_id = $sale->id;
                    $sale_product->store_id = $store_id;
                    $sale_product->category_id =$product->category_id;
                    $sale_product->unit_id = $product->unit_id;
                    $sale_product->product_id = $product_id[$i];
                    $sale_product->qty = $p_qty;
                    $sale_product->sale_price = $p_sale_price;
                    $sale_product->total = $sub_total;
                    $sale_product->product_vat = $product_vat;
                    $sale_product->product_vat_amount = $product_vat_amount;
                    $sale_product->product_discount_type = $discount_type;
                    $sale_product->per_product_discount = $per_product_discount;
                    $sale_product->product_discount_percent = $discount_percent;
                    $sale_product->product_discount = $final_discount_amount;
                    $sale_product->after_product_discount = ($sub_total + $product_vat_amount) - $final_discount_amount;
                    $sale_product->product_total = ($sub_total + $product_vat_amount) - $final_discount_amount;
                    $sale_product->per_product_profit = $per_qty_profit_amount;
                    $sale_product->total_profit = $total_amount;
                    $sale_product->created_by_user_id = Auth::User()->id;
                    $sale_product->save();
                }
                if($package_id){
                    $sale_package = new SalePackage();
                    $sale_package->sale_id = $sale->id;
                    $sale_package->store_id = $store_id;
                    $sale_package->package_id = $package_id;
                    $sale_package->amount = Package::whereid($package_id)->pluck('amount')->first();
                    $sale_package->created_by_user_id = Auth::User()->id;
                    $sale_package->save();
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
                    $payment_receipt->date = date('Y-m-d');
                    $payment_receipt->store_id = $store_id;
                    $payment_receipt->order_type = 'Sale';
                    $payment_receipt->order_id = $sale->id;
                    $payment_receipt->customer_id = $customer_id;
                    $payment_receipt->order_type_id = 1;
                    if($request->payment_type_id != NULL){
                        $payment_receipt->payment_type_id =  $request->payment_type_id;
                    }
                    $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                    $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                    $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                    $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                    $payment_receipt->note = $request->note ? $request->note : '';
                    $payment_receipt->total = $grand_total;
                    $payment_receipt->amount = $paid_amount;
                    $payment_receipt->due = $due_amount;
                    $payment_receipt->receipt_time = 'Sale';
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                // }

                // for due amount > 0
                // if($due_amount > 0){
                //     $payment_receipt = new PaymentReceipt();
                //     $payment_receipt->invoice_no = $invoice_no;
                //     $payment_receipt->date = date('Y-m-d');
                //     $payment_receipt->store_id = $store_id;
                //     $payment_receipt->order_type = 'Sale';
                //     $payment_receipt->order_id = $sale->id;
                //     $payment_receipt->customer_id = $customer_id;
                //     $payment_receipt->order_type_id = 2;
                //     if($request->payment_type_id != NULL){
                //         $payment_receipt->payment_type_id =  $request->payment_type_id;
                //     }
                //     $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                //     $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                //     $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                //     $payment_receipt->amount = $due_amount;
                //     $payment_receipt->created_by_user_id = Auth::User()->id;
                //     $payment_receipt->save();
                // }

                // Operation Log Success
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Sale','Update',$previous_data,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Sale Updated Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.sales.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Sale','Update',$previous_data,$current_data,'Error',$activity_id,$e->getMessage());

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
        $data = Sale::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);
        $data2 = SaleProduct::wheresale_id($id)->get();
        $decode_data2 = json_decode($data2->toJson());
        array_push($previous_data,$decode_data2);
        $sale = Sale::find($id);
        $store_id = $sale->store_id;

        try {
            DB::beginTransaction();
            DB::table('payment_receipts')->where('order_id',$id)->where('order_type','Sale')->delete();
            DB::table('sale_products')->where('sale_id',$id)->delete();
            DB::table('sale_packages')->where('sale_id',$id)->delete();
            DB::table('advance_receipts')->where('sale_id',$id)->delete();
            $sale->delete();

            // Operation Log Success
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Sale','Delete',$previous_data,NULL,$status,NULL,NULL);

            DB::commit();
            Toastr::success("Sale Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.sales.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Sale','Delete',$previous_data,NULL,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
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

    public function salePrintWithPageSize($id, $pagesize)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $sale = Sale::findOrFail($id);
        $created_by_user_id = $sale->created_by_user_id;
        $saleProducts = SaleProduct::where('sale_id', $id)->get();
        $refundable_amount = SaleReturn::where('sale_id', $id)->sum('refundable_amount');
        $refund_amount = SaleReturn::where('sale_id', $id)->sum('refund_amount');
        $due_amount = SaleReturn::where('sale_id', $id)->sum('due_amount');
        $saleReturnProducts = SaleReturnDetail::where('sale_id', $id)->get();
        $previousDue= Sale::where('id','!=',$id)->wherecustomer_id($sale->customer_id)->sum('due_amount');
        $transactions = PaymentReceipt::where('order_id',$id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id','!=',NULL)->get();
        return view('backend.common.sales.print_with_size', compact('sale', 'saleProducts', 'pagesize','previousDue','refundable_amount','refund_amount','due_amount','saleReturnProducts','default_currency','digit','default_business_settings','transactions','created_by_user_id'));
    }

    public function saleInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $sale = Sale::findOrFail($id);
        $created_by_user_id = $sale->created_by_user_id;
        $saleProducts = SaleProduct::where('sale_id', $id)->get();
        $refundable_amount = SaleReturn::where('sale_id', $id)->sum('refundable_amount');
        $refund_amount = SaleReturn::where('sale_id', $id)->sum('refund_amount');
        $due_amount = SaleReturn::where('sale_id', $id)->sum('due_amount');
        $saleReturnProducts = SaleReturnDetail::where('sale_id', $id)->get();
        $previousDue= Sale::where('id','!=',$id)->wherecustomer_id($sale->customer_id)->sum('due_amount');
        $transactions = PaymentReceipt::where('order_id',$id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id','!=',NULL)->get();
        $pdf = Pdf::loadView('backend.common.sales.invoice_pdf', compact('sale', 'saleProducts','previousDue','refundable_amount','refund_amount','due_amount','saleReturnProducts','digit','default_business_settings','transactions','created_by_user_id'));
        return $pdf->stream('saleinvoice_' . now() . '.pdf');
    }
}
