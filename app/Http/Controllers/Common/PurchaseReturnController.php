<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\PurchaseReturn;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\PurchaseReturnDetail;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;

class PurchaseReturnController extends Controller
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
        // $this->middleware('permission:purchase-returns-list', ['only' => ['index', 'show']]);
        // $this->middleware('permission:purchase-returns-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:purchase-returns-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:purchase-returns-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        try {
            $User=$this->User;
            if ($request->ajax()) {

                if ($User->user_type == 'Super Admin') {
                    $purchaseReturns = PurchaseReturn::orderBy('id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $purchaseReturns = PurchaseReturn::wherestore_id($User->store_id)->orderBy('id', 'DESC');
                }else{
                    $purchaseReturns = PurchaseReturn::wherecreated_by_user_id($User->id)->orderBy('id', 'DESC');
                }
                return Datatables::of($purchaseReturns)
                    ->addIndexColumn()
                    ->addColumn('store', function ($data) {
                        return $data->store->name;
                    })
                    ->addColumn('supplier', function ($data) {
                        return $data->supplier->name;
                    })
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'purchase-returns\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'purchase-returns\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($purchase_return)use($User) {
                        $btn='';
                        if($User->can('purchase-returns-edit')){
                        $btn = '<a href=' . route(\Request::segment(1) . '.purchase-returns.edit', $purchase_return->id) . ' class="btn btn-info btn-sm waves-effect"><i class="fa fa-edit"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['category','action', 'status'])
                    ->make(true);
            }

            return view('backend.common.purchase_returns.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->pluck('name','id');
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->pluck('name','id');
        }
        $suppliers = Supplier::wherestatus(1)->pluck('name','id');
        $categories = Category::wherestatus(1)->get();
        return view('backend.common.purchase_returns.create', compact('stores','suppliers','categories'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'return_date' => 'required',
            'store_id' => 'required',
            'supplier_id' => 'required',
            'total_quantity' => 'required',
            'product_category_id.*' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $sale = Purchase::findOrFail($request->sale_id);
            $purchase_return = new PurchaseReturn();
            $purchase_return->return_date = $request->return_date;
            $purchase_return->store_id = $request->store_id;
            $purchase_return->supplier_id = $request->supplier_id;
            $purchase_return->total_quantity = $request->total_quantity;
            $purchase_return->comments = $request->comments;
            $purchase_return->status = 1;
            $purchase_return->created_by_user_id = Auth::User()->id;
            $insert_id = $purchase_return->save();
            if($insert_id){
                for($i=0; $i<count($request->category_id); $i++){
                    $saleProduct = Stock::wherepurchase_id($sale->id)->whereproduct_id($request->product_id[$i])->first();
                    $purchase_return_detail = new PurchaseReturnDetail();
                    $purchase_return_detail->purchase_return_id = $purchase_return->id;
                    $purchase_return_detail->store_id = $request->store_id;
                    $purchase_return_detail->category_id = $request->category_id[$i];
                    $purchase_return_detail->product_id = $request->product_id[$i];
                    $purchase_return_detail->qty = $request->qty[$i];
                    $purchase_return_detail->status = 1;
                    $purchase_return_detail->created_by_user_id = Auth::User()->id;
                    $purchase_return_detail->save();
                }
            }
            DB::commit();
            Toastr::success("PurchaseReturn Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.purchase-returns.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function show($id)
    {
        $purchase_return = PurchaseReturn::findOrFail($id);
        return view('backend.common.purchase_returns.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase_return = PurchaseReturn::findOrFail($id);
        $packageProducts = Stock::wherepackage_id($id)->get();
        $categories = Category::wherestatus(1)->get();
        $products = Product::wherestatus(1)->get();
        return view('backend.common.purchase_returns.edit', compact('purchase','packageProducts','categories','products'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $this->validate($request, [
            'return_date' => 'required',
            'store_id' => 'required',
            'supplier_id' => 'required',
            'total_quantity' => 'required',
            'product_category_id.*' => 'required',
            'product_id.*' => 'required',
            'qty.*' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $purchase_return = PurchaseReturn::findOrFail($id);
            $purchase_return->name = $request->name;
            $purchase_return->amount = $request->amount;
            $purchase_return->updated_by_user_id = Auth::User()->id;
            $update_purchase_return = $purchase_return->save();
            if($update_purchase_return){
                DB::table('package_products')->wherepackage_id($id)->delete();
                for($i=0; $i<count($request->category_id); $i++){
                    $purchase_return_detail = new Stock();
                    $purchase_return_detail->package_id = $id;
                    $purchase_return_detail->product_id = $request->product_id[$i];
                    $purchase_return_detail->qty = $request->qty[$i];
                    $purchase_return_detail->created_by_user_id = Auth::User()->id;
                    $purchase_return_detail->updated_by_user_id = Auth::User()->id;
                    $purchase_return_detail->save();
                }
            }
            DB::commit();
            Toastr::success("PurchaseReturn Updated Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.purchase-returns.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function destroy($id)
    {
        //
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
