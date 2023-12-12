<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use App\Models\PaymentReceipt;
use App\Models\Customer;
use App\Models\Store;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Helpers\ErrorTryCatch;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\BusinessSettingTrait;
use NumberFormatter;
use App\Http\Resources\CustomerLedgerCollection;
use App\Http\Traits\CurrencyTrait;

class AllCustomerLedgerController extends Controller
{
    use BusinessSettingTrait;
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
        // $this->middleware('permission:customer-ledgers-list', ['only' => ['index', 'show']]);
        // $this->middleware('permission:customer-ledgers-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:customer-ledgers-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:customer-ledgers-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        try {

            $customers = Customer::wherestatus(1)->get();
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view('backend.common.all_customer_ledgers.index', compact('customers','stores'));
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // try {
            $default_business_settings = $this->getBusinessSettingsInfo();
            $default_currency = $this->getCurrencyInfoByDefaultCurrency();
            $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
            $customer_id = $request->customer_id;
            $store_id = $request->store_id;
            $status = $request->status;
            $customers = Customer::wherestatus(1)->get();
            $stores = Store::wherestatus(1)->get();
            $customer = Customer::find($customer_id);


            // $allCustomerLedgers_data = Sale::with('customer')->select('customer_id')
            // ->groupBy('customer_id');
            // if($store_id != 'All'){
            //     $allCustomerLedgers_data->wherestore_id($store_id);
            // }
            // if($customer_id != 'All'){
            //     $allCustomerLedgers_data->wherecustomer_id($customer_id);
            // }
            // if($status != 'All'){
            //     $allCustomerLedgers_data->where('due_amount', '>', 0);
            // }
            // $sales = $allCustomerLedgers_data->get();

            $allCustomerLedgers_data = PaymentReceipt::with('customer')->select('customer_id')
            ->where('customer_id', '!=', null)
            ->where(function ($query) {
                $query->whereorder_type('Sale')
                    ->orWhere('order_type', 'Received')
                    ->orWhere('order_type', 'Previous Due');
            })
            ->groupBy('customer_id');
            if($store_id != 'All'){
                $allCustomerLedgers_data->wherestore_id($store_id);
            }
            if($customer_id != 'All'){
                $allCustomerLedgers_data->wherecustomer_id($customer_id);
            }
            if($status != 'All'){
                $allCustomerLedgers_data->where('due', '>', 0);
            }
            $sales = $allCustomerLedgers_data->get();

            if($store_id == 'All'){
                $saleReturns = SaleReturn::wherecustomer_id($customer_id)->get();
            }else{
                $saleReturns = SaleReturn::wherecustomer_id($customer_id)->where('store_id', '=', $store_id)->get();
            }


            // dd($sales);




            $pdf = Pdf::loadView('backend.common.all_customer_ledgers.pdf_view_3', compact('sales', 'store_id', 'default_currency','default_business_settings','customer','customer_id','saleReturns'));



            return $pdf->stream('customer_ledger_' . now() . '.pdf');

        // } catch (\Exception $e) {
        //     $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
        //     Toastr::error($response['message'], "Error");
        //     return back();
        // }
    }
    public function show($id)
    {

    }

    public function edit($id)
    {
    }


    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
