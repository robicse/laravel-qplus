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
use App\Helpers\Helper;

class CustomerDueController extends Controller
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
                    $sales = Sale::with('store','customer','payment_type')
                    ->where('sales.due_amount','>',0)
                    ->orderBy('sales.id', 'DESC');
                }else if ($User->user_type == 'Admin') {
                    $sales = Sale::with('store','customer','payment_type')
                    ->wherestore_id($User->store_id)
                    ->where('sales.due_amount','>',0)
                    ->orderBy('sales.id', 'DESC');
                }else{
                    $sales = Sale::with('store','customer','payment_type')
                    ->wherecreated_by_user_id($User->id)
                    ->where('sales.due_amount','>',0)
                    ->orderBy('sales.id', 'DESC');
                }

            return Datatables::of($sales)
                ->addIndexColumn()
                ->addColumn('customer_due_amount', function ($data) {
                    $store_id = 1;
                    return Helper::customerDueAmount($store_id,$customer_id);
                })
                // ->addColumn('created_by_user', function ($data) {
                //     return $data->created_by_user->name;
                // })
                ->make(true);
            }
            return view('backend.common.customer_dues.index');
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
