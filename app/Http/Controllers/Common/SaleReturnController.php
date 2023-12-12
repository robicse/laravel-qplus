<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PaymentReceipt;
use App\Models\OrderType;
use App\Models\PaymentType;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Product;
use App\Models\Store;
use App\Models\Package;
use App\Models\SaleProduct;
use App\Models\Customer;
use App\Models\SaleReturnDetail;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use App\Helpers\Helper;
use DataTables;
use NumberFormatter;
use App\Http\Traits\BusinessSettingTrait;
use Illuminate\Support\Facades\Redirect;

class SaleReturnController extends Controller
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
        // $this->middleware('permission:sale-returns-list', ['only' => ['index', 'show']]);
        // $this->middleware('permission:sale-returns-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:sale-returns-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:sale-returns-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        try {
            $User=$this->User;
            if ($request->ajax()) {
                if ($User->user_type == 'Super Admin') {
                    $saleReturns = SaleReturn::orderBy('id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $saleReturns = SaleReturn::wherestore_id($User->store_id)->orderBy('id', 'DESC');
                }else{
                    $saleReturns = SaleReturn::wherecreated_by_user_id($User->id)->orderBy('id', 'DESC');
                }

                return Datatables::of($saleReturns)
                    ->addIndexColumn()
                    ->addColumn('store', function ($data) {
                        return $data->store->name;
                    })
                    ->addColumn('customer', function ($data) {
                        return $data->customer->name;
                    })
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'sale-returns\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'sale-returns\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($sale_return)use($User) {
                        $btn='';
                        $btn .= '<span  class="d-inline-flex"><a href=' . route(\Request::segment(1) . '.sale-returns.show', $sale_return->id) . ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                        if($User->can('sale-returns-edit')){
                        $btn .= '<a href=' . route(\Request::segment(1) . '.sale-returns.edit', $sale_return->id) . ' class="btn btn-info btn-sm waves-effect float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                        }
                        $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . '/sale-returns-prints/' . $sale_return->id . '/a4') . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fa fa-print"></i></a>';
                        $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/sale-returns-invoice-pdf/" . $sale_return->id) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a>';
                        $btn .= '<form method="post" action=' . route(\Request::segment(1) . '.sale-returns.destroy',$sale_return->id) . '>'.csrf_field().'<input type="hidden" name="_method" value="DELETE">';
                        $btn .= '<button class="btn btn-sm btn-danger" style="margin-left: 5px;" type="submit" onclick="return confirm(\'You Are Sure This Delete !\')"><i class="fa fa-trash"></i></button>';
                        $btn .= '</form></span>';

                        return $btn;
                    })
                    ->rawColumns(['category','action', 'status'])
                    ->make(true);
            }

            return view('backend.common.sale_returns.index');
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

        $sales = Sale::wherestatus(1)->pluck('id','id');
        return view('backend.common.sale_returns.create', compact('stores','customers','categories','units','packages','cash_payment_types','credit_payment_types','order_types','sales'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'sale_id' => 'required',
            'return_date' => 'required',
            //'store_id' => 'required',
            //'customer_id' => 'required',
            'total_quantity' => 'required',
            'product_category_id.*' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required'
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
            $grand_total = $request->grand_total;
            $customer_due = $request->customer_due;
            $refundable_amount = $request->refundable_amount;
            $paid_amount = $request->paid;
            $due_amount = $request->due;
            $return_date = $request->return_date;

            $sale = Sale::findOrFail($request->sale_id);
            $sale_return = new SaleReturn();
            $sale_return->return_date = $return_date;
            $sale_return->sale_id = $sale->id;
            $sale_return->store_id = $sale->store_id;
            $sale_return->customer_id = $sale->customer_id;
            $sale_return->order_type_id = $request->sale_type_id;
            $sale_return->total_quantity = $request->total_quantity;
            $sale_return->comments = $request->comments;
            $sale_return->grand_total = $grand_total;
            $sale_return->customer_due = $customer_due;
            $sale_return->refundable_amount = $refundable_amount;
            $sale_return->refund_amount = $paid_amount;
            $sale_return->due_amount = $due_amount;
            if($request->payment_type_id != NULL){
                $sale_return->payment_type_id =  $request->payment_type_id;
            }else{
                $sale_return->payment_type_id =  4;
            }
            $sale_return->bank_name = $request->bank_name ? $request->bank_name : '';
            $sale_return->cheque_number = $request->cheque_number ? $request->cheque_number : '';
            $sale_return->cheque_date = $request->cheque_date ? $request->cheque_date : '';
            $sale_return->transaction_number = $request->transaction_number ? $request->transaction_number : '';
            $sale_return->note = $request->note ? $request->note : '';
            $sale_return->status = 1;
            $sale_return->created_by_user_id = Auth::User()->id;
            $insert_id = $sale_return->save();
            if($insert_id){
                // sale update
                if($customer_due){
                    $sale->due_amount -= $customer_due;
                    $sale->advance_minus_amount += $customer_due;
                    $sale->save();
                }

                $profit_minus_amount = 0;
                for($i=0; $i<count($request->product_id); $i++){
                    $saleProduct = SaleProduct::wheresale_id($sale->id)->whereproduct_id($request->product_id[$i])->first();
                    $profit_minus_amount += $saleProduct->per_product_profit * $request->qty[$i];
                    $sale_return_detail = new SaleReturnDetail();
                    $sale_return_detail->sale_return_id = $sale_return->id;
                    $sale_return_detail->store_id = $sale->store_id;
                    // $sale_return_detail->category_id = $request->category_id[$i];
                    $sale_return_detail->product_id = $request->product_id[$i];
                    $sale_return_detail->qty = $request->qty[$i];
                    $sale_return_detail->amount = $saleProduct->sale_price;
                    $sale_return_detail->profit_minus = $saleProduct->total_profit;
                    $sale_return_detail->created_by_user_id = Auth::User()->id;
                    $sale_return_detail->save();

                    $previous_already_return_qty = $saleProduct->already_return_qty;
                    $previous_total_profit = $saleProduct->total_profit;
                    // sale product
                    $saleProduct->total_profit = ($previous_total_profit - $profit_minus_amount);
                    $saleProduct->already_return_qty = ($previous_already_return_qty + $request->qty[$i]);
                    $saleProduct->save();
                }

                $sale_return->profit_minus_amount = $profit_minus_amount;
                $sale_return->update();

                // PaymentReceipt::whereorder_id($sale->id)->whereorder_type('Sale')->whereorder_type_id(2)->delete();

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
                    $payment_receipt->date = $return_date;
                    $payment_receipt->store_id = $sale->store_id;
                    $payment_receipt->order_type = 'Sale Return';
                    $payment_receipt->order_id = $sale_return->id;
                    $payment_receipt->customer_id = $sale->customer_id;
                    $payment_receipt->order_type_id = 1;
                    if($request->payment_type_id != NULL){
                        $payment_receipt->payment_type_id =  $request->payment_type_id;
                    }
                    $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                    $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                    $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                    $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                    $payment_receipt->note = $request->note ? $request->note : '';
                    $payment_receipt->total = $refundable_amount;
                    $payment_receipt->amount = $paid_amount;
                    $payment_receipt->due = $due_amount;
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                }

                // for due amount > 0
                // if($due_amount > 0){
                //     $payment_receipt = new PaymentReceipt();
                //     $payment_receipt->invoice_no = $invoice_no;
                //     $payment_receipt->date = $return_date;
                //     $payment_receipt->store_id = $sale->store_id;
                //     $payment_receipt->order_type = 'Sale Return';
                //     $payment_receipt->order_id = $sale->id;
                //     $payment_receipt->customer_id = $sale->customer_id;
                //     $payment_receipt->order_type_id = 2;
                //     if($request->payment_type_id != NULL){
                //         $payment_receipt->payment_type_id =  $request->payment_type_id;
                //     }
                //     $payment_receipt->bank_name = $request->bank_name ? $request->bank_name : '';
                //     $payment_receipt->cheque_number = $request->cheque_number ? $request->cheque_number : '';
                //     $payment_receipt->cheque_date = $request->cheque_date ? $request->cheque_date : '';
                //     $payment_receipt->transaction_number = $request->transaction_number ? $request->transaction_number : '';
                //     $payment_receipt->note = $request->note ? $request->note : '';
                //     $payment_receipt->amount = $due_amount;
                //     $payment_receipt->created_by_user_id = Auth::User()->id;
                //     $payment_receipt->save();
                // }

                // Operation Log Success
                $activity_id = $sale_return->id;
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Sale Return','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("SaleReturn Created Successfully", "Success");
            // return redirect()->route(\Request::segment(1) . '.sale-returns.index');
            return Redirect::to(\Request::segment(1) . '/sale-returns-prints/' . $sale_return->id . '/a4');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Sale Return','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function show($id)
    {
        $SaleReturn = SaleReturn::findOrFail($id);
        $store= Store::findOrFail($SaleReturn->store_id);
        $SaleReturnDetails = SaleReturnDetail::wheresale_return_id($SaleReturn->id)->get();
        return view('backend.common.sale_returns.show', compact('store','SaleReturn','SaleReturnDetails'));
    }

    public function edit($id)
    {
        $sale = SaleReturn::with('customer')->findOrFail($id);
        $saleDetails = SaleDetails::where('sale_id', $id)->get();

        $payment_types = PaymentType::where('name', '!=', 'LC')->get();
        return view('backend.common.sale_returns.edit', compact('sale', 'saleDetails', 'vans', 'salesmans', 'payment_types'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $this->validate($request, [
            'return_date' => 'required',
            'store_id' => 'required',
            'customer_id' => 'required',
            'total_quantity' => 'required',
            'product_category_id.*' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required'
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = SaleReturn::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);
        // $data2 = SaleProduct::wheresale_id($id)->get();
        // $decode_data2 = json_decode($data2->toJson());
        // array_push($previous_data,$decode_data2);

        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();
            $sale_return = SaleReturn::findOrFail($id);
            $sale_return->name = $request->name;
            $sale_return->amount = $request->amount;
            $sale_return->updated_by_user_id = Auth::User()->id;
            $update_sale_return = $sale_return->save();
            if($update_sale_return){
                DB::table('package_products')->wherepackage_id($id)->delete();
                for($i=0; $i<count($request->category_id); $i++){
                    $sale_return_detail = new Stock();
                    $sale_return_detail->package_id = $id;
                    $sale_return_detail->product_id = $request->product_id[$i];
                    $sale_return_detail->qty = $request->qty[$i];
                    $sale_return_detail->amount = $request->qty[$i];
                    $sale_return_detail->profit_minus = $request->qty[$i];
                    $sale_return_detail->created_by_user_id = Auth::User()->id;
                    $sale_return_detail->updated_by_user_id = Auth::User()->id;
                    $sale_return_detail->save();
                }

                // Operation Log Success
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Sale Return','Update',$previous_data,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("SaleReturn Updated Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.sale-returns.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Sale Return','Update',$previous_data,$current_data,'Error',$activity_id,$e->getMessage());

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
        $data = SaleReturn::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);
        $data2 = SaleReturnDetail::wheresale_return_id($id)->get();
        $decode_data2 = json_decode($data2->toJson());
        array_push($previous_data,$decode_data2);
        $store_id = $data->store_id;

        try {
            DB::beginTransaction();
            $saleReturn = SaleReturn::find($id);
            $saleReturnDetails= DB::table('sale_return_details')->where('sale_return_id',$id)->get();
            $countSaleReturnDetails = count($saleReturnDetails);
            if($countSaleReturnDetails > 0){
                foreach($saleReturnDetails as $saleReturnDetail){
                    $saleProduct = SaleProduct::wheresale_id($saleReturn->sale_id)->whereproduct_id($saleReturnDetail->product_id)->first();
                    if($saleProduct){
                        $exists_qty = $saleProduct->already_return_qty;
                        $saleProduct->already_return_qty = $exists_qty - $saleReturnDetail->qty;
                        $saleProduct->save();
                    }
                }
            }
            DB::table('sale_return_details')->where('sale_return_id',$id)->delete();
            DB::table('payment_receipts')->where('order_id',$id)->whereorder_type('Sale Return')->delete();
            $saleReturn->delete();

            // Operation Log Success
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Sale Return','Delete',$previous_data,NULL,$status,NULL,NULL);

            DB::commit();
            Toastr::success("SaleReturn Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.sale-returns.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Sale Return','Delete',$previous_data,NULL,'Error',NULL,$e->getMessage());

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

    public function saleInfo(Request $request)
    {
        $sale = Sale::findOrFail($request->sale_id);
        $store_id = $sale->store_id;
        $customer_due = Helper::customerDueAmount($store_id,$sale->customer_id);
        $options = [
            'storeOptions' => '',
            'customerOptions' => '',
            'customer_due' => $customer_due,
        ];
        $store = Store::findOrFail($sale->store_id);
        if($store){
            $options['storeOptions'] .= "<option value='$store->id'>" . $store->name . "</option>";
        }
        $customer = Customer::findOrFail($sale->customer_id);
        if($customer){
            $options['customerOptions'] .= "<option value='$customer->id'>" . $customer->name . "</option>";
        }
        return response()->json(['success' => true, 'data' => $options]);
    }

    public function saleReturnPrintWithPageSize($id, $pagesize)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $saleReturn = SaleReturn::findOrFail($id);
        $created_by_user_id = $saleReturn->created_by_user_id;
        $saleReturnProducts = SaleReturnDetail::where('sale_return_id', $id)->get();
        $transactions = PaymentReceipt::where('order_id',$id)->where('order_type','Sale Return')->where('order_type_id',1)->where('payment_type_id','!=',NULL)->get();
        return view('backend.common.sale_returns.print_with_size', compact('saleReturn', 'saleReturnProducts', 'pagesize','digit','default_business_settings','transactions','created_by_user_id'));
    }

    public function saleReturnInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $saleReturn = SaleReturn::findOrFail($id);
        $created_by_user_id = $saleReturn->created_by_user_id;
        $saleReturnProducts = SaleReturnDetail::where('sale_return_id', $id)->get();
        $transactions = PaymentReceipt::where('order_id',$id)->where('order_type','Sale Return')->where('payment_type_id','!=',NULL)->get();
        $pdf = Pdf::loadView('backend.common.sale_returns.invoice_pdf', compact('saleReturn', 'saleReturnProducts','digit','default_business_settings','transactions','created_by_user_id'));
        return $pdf->stream('sale_return_invoice_' . now() . '.pdf');
    }
}
