<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\Helper;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;

class StockController extends Controller
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
        $this->middleware('permission:stocks-list', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        try {
            $User=$this->User;
            if ($request->ajax()) {

                if ($User->user_type == 'Super Admin') {
                    $stocks = Stock::with('purchase','product','store')->latest();
                }else if ($User->user_type == 'Admin') {
                    $stocks = Stock::with('purchase','product','store')->wherestore_id($User->store_id)->latest();
                }else{
                    $stocks = Stock::with('purchase','product','store')->wherecreated_by_user_id($User->id)->latest();
                }
                return Datatables::of($stocks)
                    ->addIndexColumn()
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }

            return view('backend.common.stocks.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }
}
