<?php

namespace App\Http\Controllers\Common;
use App\Helpers\Helper;
use DB;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;

class ProductController extends Controller
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
        $this->middleware('permission:products-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:products-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:products-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:products-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        try {
            $User=$this->User;
            if ($request->ajax()) {
                $products = Product::orderBy('id', 'DESC');
                return Datatables::of($products)
                    ->addIndexColumn()
                    ->addColumn('category', function ($products) {
                        return $products?->category?->name;
                    })
                    ->addColumn('unit', function ($products) {
                        return $products?->unit?->name;
                    })
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'products\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'products\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($product)use($User) {
                        $btn='';
                        if($User->can('products-edit')){
                        $btn = '<a href=' . route(\Request::segment(1) . '.products.edit', $product->id) . ' class="btn btn-info btn-sm waves-effect"><i class="fa fa-edit"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['category','action', 'status'])
                    ->make(true);
            }

            return view('backend.common.products.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        $categories = Category::pluck('name','id');
        $units = Unit::pluck('name','id');
        return view('backend.common.products.create', compact('categories','units'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|min:1|max:190|unique:products',
            'category_id' => 'required',
            'unit_id' => 'required'
        ]);

        // Operation Log initialize
        $activity_id = NULL;
        $status = 'Failed';
        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = NULL;

        try {
            DB::beginTransaction();
            $product = new Product();
            $product->category_id = $request->category_id;
            $product->unit_id = $request->unit_id;
            $product->name = $request->name;
            $product->stock_low_qty = $request->stock_low_qty;
            $product->created_by_user_id = Auth::User()->id;
            $product->save();

            // Operation Log Success
            $activity_id = $product->id;
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Product','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Product Created Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.products.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Product','Create',NULL,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('backend.common.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::pluck('name','id');
        $units = Unit::pluck('name','id');
        return view('backend.common.products.edit', compact('product','categories','units'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => "required|min:1|max:190|unique:products,name,$id",
            'category_id' => 'required',
            'unit_id' => 'required'
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = Product::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);

        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = NULL;

        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->unit_id = $request->unit_id;
            $product->name = $request->name;
            $product->stock_low_qty = $request->stock_low_qty;
            $product->updated_by_user_id = Auth::User()->id;
            $product->save();

            // Operation Log Success
            $status = 'Success';
            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Product','Update',$previous_data,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Product Updated Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.products.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Product','Update',$previous_data,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function destroy($id)
    {
        //
    }
}
