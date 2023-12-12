<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\CurrencyTrait;
use App\Models\PaymentReceipt;
use App\Models\OrderType;
use App\Models\PaymentType;
use App\Models\Category;
use App\Models\Unit;
use App\Models\BlankSale;
use App\Models\BlankSaleProduct;
use App\Models\BlankSalePackage;
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

class BlankSaleController extends Controller
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
        // $this->middleware('permission:blank-blank_sales-list', ['only' => ['index', 'show']]);
        // $this->middleware('permission:blank-blank_sales-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:blank-blank_sales-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:blank-blank_sales-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        try {
            $User=$this->User;
            if ($request->ajax()) {

                if ($User->user_type == 'Super Admin') {
                    $blank_sales = BlankSale::orderBy('id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $blank_sales = BlankSale::wherestore_id($User->store_id)->orderBy('id', 'DESC');
                }else{
                    $blank_sales = BlankSale::wherecreated_by_user_id($User->id)->orderBy('id', 'DESC');
                }
                return Datatables::of($blank_sales)
                    ->addIndexColumn()
                    ->addColumn('store', function ($data) {
                        return $data->store->name;
                    })
                    ->addColumn('customer', function ($data) {
                        return $data->customer->name;
                    })
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'blank_sales\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'blank_sales\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($blank_sale)use($User) {
                        $btn='';
                        $btn = '<span  class="d-inline-flex"><a href=' . route(\Request::segment(1) . '.blank-sales.show', $blank_sale->id) . ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                        $btn .= '<a target="_blank" href=' . url(\Request::segment(1) . "/blank-sales-invoice-pdf/" . $blank_sale->id) . ' class="btn btn-info  btn-sm float-left" style="margin-left: 5px"><i class="fas fa-file-pdf"></i></a>';
                        $btn .= '<a href=' . route(\Request::segment(1) . '.blank-sales.edit', $blank_sale->id) . ' class="btn btn-info btn-sm waves-effect float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                        $btn .= '<form method="post" action=' . route(\Request::segment(1) . '.blank-sales.destroy',$blank_sale->id) . '">'.csrf_field().'<input type="hidden" name="_method" value="DELETE">';
                        $btn .= '<button class="btn btn-sm btn-danger" style="margin-left: 5px;" type="submit" onclick="return confirm(\'You Are Sure This Delete !\')"><i class="fa fa-trash"></i></button>';
                        $btn .= '</form></span>';

                        return $btn;
                    })
                    ->rawColumns(['category','action', 'status'])
                    ->make(true);
            }

            return view('backend.common.blank_sales.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        $order_types = OrderType::whereIn('name', ['Cash', 'Credit'])->get();
        $cash_payment_types = PaymentType::whereIn('name', ['Cash', 'Card', 'Online'])->get();
        $credit_payment_types = PaymentType::whereIn('name', ['Cheque', 'Condition'])->get();
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $customers = Customer::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        $units = Unit::wherestatus(1)->get();
        $packages = Package::wherestatus(1)->pluck('name','id');
        return view('backend.common.blank_sales.create', compact('stores','customers','categories','units','packages','order_types','cash_payment_types','credit_payment_types'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'voucher_date' => 'required',
            'store_id' => 'required',
            'customer_id' => 'required',
            'total_quantity' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required',
        ]);

        // try {
            DB::beginTransaction();
            $voucher_date = $request->voucher_date;
            $store_id = $request->store_id;
            $customer_id = $request->customer_id;
            $total_quantity = $request->total_quantity;
            $product_id = $request->product_id;
            $unit_id = $request->unit_id;
            $qty = $request->qty;
            $sale_price = $request->sale_price;
            $package_id = $request->package_id;

            $blank_sale = new BlankSale();
            $blank_sale->voucher_date = $voucher_date;
            $blank_sale->store_id = $store_id;
            $blank_sale->customer_id = $customer_id;
            $blank_sale->total_quantity = $total_quantity;
            $blank_sale->sub_total = $request->sub_total;
            $blank_sale->grand_total = $request->sub_total;
            $blank_sale->order_type_id = $request->order_type_id;
            $blank_sale->payment_type_id = $request->payment_type_id;
            $blank_sale->status = 1;
            $blank_sale->created_by_user_id = Auth::User()->id;
            $insert_id = $blank_sale->save();
            if($insert_id){
                for($i=0; $i<count($product_id); $i++){
                    $product = Product::whereid($product_id[$i])->first();
                    $sale_product = new BlankSaleProduct();
                    $sale_product->blank_sale_id = $blank_sale->id;
                    $sale_product->store_id = $store_id;
                    $sale_product->category_id =$product->category_id;
                    $sale_product->unit_id = $product->unit_id;
                    $sale_product->product_id = $product_id[$i];
                    $sale_product->qty = $qty[$i];
                    $sale_product->sale_price = $sale_price[$i];
                    $sale_product->total = $qty[$i] * $sale_price[$i];
                    $sale_product->created_by_user_id = Auth::User()->id;
                    $sale_product->save();
                }
                if($package_id){
                    $sale_package = new BlankSalePackage();
                    $sale_package->blank_sale_id = $blank_sale->id;
                    $sale_package->store_id = $store_id;
                    $sale_package->package_id = $package_id;
                    $sale_package->created_by_user_id = Auth::User()->id;
                    $sale_package->save();
                }
            }
            DB::commit();
            Toastr::success("BlankSale Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.blank-sales.index');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
        //     Toastr::error($response['message'], "Error");
        //     return back();
        // }
    }

    public function show($id)
    {
        $blankSale = BlankSale::findOrFail($id);
        $blankSaleDetails = BlankSaleProduct::where('blank_sale_id', $id)->get();
        return view('backend.common.blank_sales.show', compact('blankSale', 'blankSaleDetails'));
    }

    public function edit($id)
    {
        $order_types = OrderType::whereIn('name', ['Cash', 'Credit'])->get();
        $cash_payment_types = PaymentType::whereIn('name', ['Cash', 'Card', 'Online'])->get();
        $credit_payment_types = PaymentType::whereIn('name', ['Cheque', 'Condition'])->get();
        $blankSale = BlankSale::with('customer')->findOrFail($id);
        $blankSaleDetails = BlankSaleProduct::where('blank_sale_id', $id)->get();
        $blankSalePackages = BlankSalePackage::where('blank_sale_id', $id)->get();
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $customers = Customer::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        $units = Unit::wherestatus(1)->get();
        $packages = Package::wherestatus(1)->pluck('name','id');
        return view('backend.common.blank_sales.edit', compact('blankSale', 'blankSaleDetails', 'blankSalePackages','stores','customers','categories','units','packages','cash_payment_types','credit_payment_types','order_types'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'voucher_date' => 'required',
            'store_id' => 'required',
            'customer_id' => 'required',
            'total_quantity' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required'
        ]);

        // try {
            DB::beginTransaction();
            $voucher_date = $request->voucher_date;
            $store_id = $request->store_id;
            $customer_id = $request->customer_id;
            $total_quantity = $request->total_quantity;

            $product_id = $request->product_id;
            $unit_id = $request->unit_id;
            $qty = $request->qty;
            $sale_price = $request->sale_price;
            $package_id = $request->package_id;

            $blank_sale = BlankSale::findOrFail($id);
            $blank_sale->voucher_date = $voucher_date;
            $blank_sale->store_id = $store_id;
            $blank_sale->customer_id = $customer_id;
            $blank_sale->total_quantity = $total_quantity;
            $blank_sale->sub_total = $request->sub_total;
            $blank_sale->grand_total = $request->sub_total;
            $blank_sale->order_type_id = $request->order_type_id;
            $blank_sale->payment_type_id = $request->payment_type_id;
            $blank_sale->status = 1;
            $blank_sale->updated_by_user_id = Auth::User()->id;
            $update_blank_sale = $blank_sale->save();
            if($update_blank_sale){
                DB::table('blank_sale_products')->where('blank_sale_id',$id)->delete();
                DB::table('blank_sale_packages')->where('blank_sale_id',$id)->delete();
                for($i=0; $i<count($product_id); $i++){
                    $product = Product::whereid($product_id[$i])->first();
                    $p_id = $product_id[$i];
                    $p_qty = $qty[$i];
                    $p_sale_price = $sale_price[$i];

                    $unit_id = $request->unit_id[$i] != NULL ? $request->unit_id[$i] : 0;
                    $sale_product = new BlankSaleProduct();
                    $sale_product->blank_sale_id = $blank_sale->id;
                    $sale_product->store_id = $store_id;
                    $sale_product->category_id =$product->category_id;
                    $sale_product->unit_id = $product->unit_id;
                    $sale_product->product_id = $product_id[$i];
                    $sale_product->qty = $p_qty;
                    $sale_product->sale_price = $p_sale_price;
                    $sale_product->total = $p_qty * $p_sale_price;
                    $sale_product->created_by_user_id = Auth::User()->id;
                    $sale_product->save();
                }
                if($package_id){
                    $sale_package = new BlankSalePackage();
                    $sale_package->blank_sale_id = $blank_sale->id;
                    $sale_package->store_id = $store_id;
                    $sale_package->package_id = $package_id;
                    $sale_package->created_by_user_id = Auth::User()->id;
                    $sale_package->save();
                }
            }
            DB::commit();
            Toastr::success("BlankSale Updated Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.blank-sales.index');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
        //     Toastr::error($response['message'], "Error");
        //     return back();
        // }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $blank_sale = BlankSale::find($id);
            DB::table('blank_sale_products')->where('blank_sale_id',$id)->delete();
            DB::table('blank_sale_packages')->where('blank_sale_id',$id)->delete();
            $blank_sale->delete();
            DB::commit();
            Toastr::success("Blank Sale Deleted Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.blank-sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
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
        $blank_sale = BlankSale::findOrFail($id);
        $saleProducts = BlankSaleProduct::where('blank_sale_id', $id)->get();
        $previousDue= BlankSale::where('id','!=',$id)->wherecustomer_id($blank_sale->customer_id)->sum('due_amount');
        return view('backend.common.blank_sales.print_with_size', compact('blank_sale', 'saleProducts', 'pagesize','previousDue','default_currency','digit','default_business_settings'));
    }

    public function blankSaleInvoicePdfDownload($id)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $blankSale = BlankSale::findOrFail($id);
        $created_by_user_id = $blankSale->created_by_user_id;
        $blankSaleProducts = BlankSaleProduct::where('blank_sale_id', $id)->get();
        $pdf = Pdf::loadView('backend.common.blank_sales.invoice_pdf', compact('blankSale', 'blankSaleProducts','digit','default_business_settings','created_by_user_id'));
        return $pdf->stream('blank_sale_invoice_' . now() . '.pdf');
    }
}
