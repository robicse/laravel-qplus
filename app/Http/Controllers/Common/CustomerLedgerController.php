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

class CustomerLedgerController extends Controller
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
            return view('backend.common.customer_ledgers.index', compact('customers','stores'));
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
        // try {
            $default_business_settings = $this->getBusinessSettingsInfo();
            $default_currency = $this->getCurrencyInfoByDefaultCurrency();
            $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
            $from = date('Y-m-d', strtotime($request->start_date));
            $to = date('Y-m-d', strtotime($request->end_date));
            $customer_id = $request->customer_id;
            $store_id = $request->store_id;
            $customers = Customer::wherestatus(1)->get();
            $stores = Store::wherestatus(1)->get();
            $customer = Customer::find($customer_id);
            $previewtype = $request->previewtype;

            // $firstEntryDate= PaymentReceipt::wherecustomer_id($customer_id)->pluck('date')->first();
            // if(empty($firstEntryDate)){

            //     Toastr::warning('No Record Found this user!','Warning');
            //     return back();
            // }
            // if($from < $firstEntryDate){
            //     Toastr::warning('You need to select date minimum:' . $firstEntryDate,'Warning');
            //     return back();
            // }

            // if($store_id == 'All'){
            //     $store = 'All';

            //     $customerReports = PaymentReceipt::wherecustomer_id($customer_id)
            //                         ->whereBetween('date', array($from, $to))
            //                         ->where(function ($query) {
            //                             $query->whereorder_type('Sale')
            //                                 ->orWhere('order_type', 'Sale Return')
            //                                 ->orWhere('order_type', 'Previous Due');
            //                         })
            //                         ->get();

            //     $openingDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(2)
            //                     ->wherecustomer_id($customer_id)
            //                     ->where('date', $from)
            //                     ->where('order_type', 'Previous Due')
            //                     ->first();

            //     $openingDueBalance = PaymentReceipt::whereorder_type_id(2)
            //                     ->wherecustomer_id($customer_id)
            //                     ->where('date', '<', $from)
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale')
            //                             ->orWhere('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');

            //     if($openingDueBalanceOnlyOpeningTime){
            //         $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
            //     }


            //     $totalSaleAmount = PaymentReceipt::wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale');
            //                     })
            //                     ->sum('amount');
            //     $totalSaleReturnAmount = PaymentReceipt::wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale Return');
            //                     })
            //                     ->sum('amount');

            //     $totalAmount = $totalSaleAmount;

            //     $paidAmount = PaymentReceipt::whereorder_type_id(1)
            //                     ->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale')
            //                         ->orWhere('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');
            //     $returnPaidAmount = PaymentReceipt::whereorder_type_id(1)
            //                     ->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale Return');
            //                     })
            //                     ->sum('amount');

            //     $dueAmount = PaymentReceipt::whereorder_type_id(2)
            //                     ->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale');
            //                     })
            //                     ->sum('amount');
            // }else{
            //     $store = Store::find($store_id);

            //     $customerReports = PaymentReceipt::wherecustomer_id($customer_id)
            //                         ->wherestore_id($store_id)
            //                         ->whereBetween('date', array($from, $to))
            //                         ->where(function ($query) {
            //                             $query->whereorder_type('Sale')
            //                                 ->orWhere('order_type', 'Sale Return')
            //                                 ->orWhere('order_type', 'Previous Due');
            //                         })
            //                         ->get();

            //     $openingDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
            //                     ->wherecustomer_id($customer_id)
            //                     ->where('date', $from)
            //                     ->where('order_type', 'Previous Due')
            //                     ->first();

            //     $openingDueBalance = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
            //                     ->wherecustomer_id($customer_id)
            //                     ->where('date', '<', $from)
            //                     ->where(function ($query) {
            //                         $query->Where('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');

            //     if($openingDueBalanceOnlyOpeningTime){
            //         $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
            //     }

            //     $totalSaleAmount = PaymentReceipt::wherestore_id($store_id)->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale');
            //                     })
            //                     ->sum('amount');
            //     $totalSaleReturnAmount = PaymentReceipt::wherestore_id($store_id)->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale Return');
            //                     })
            //                     ->sum('amount');
            //     $totalAmount = $totalSaleAmount - $totalSaleReturnAmount;
            //     $paidAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(1)
            //                     ->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale')
            //                         ->orWhere('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');
            //     $returnPaidAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(1)
            //                     ->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale Return');
            //                     })
            //                     ->sum('amount');
            //     $dueAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
            //                     ->wherecustomer_id($customer_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Sale');
            //                     })
            //                     ->sum('amount');
            // }

            // $pdf = Pdf::loadView('backend.common.customer_ledgers.pdf_view', compact('customerReports', 'openingDueBalance','totalAmount', 'paidAmount', 'returnPaidAmount', 'dueAmount', 'customers', 'from', 'to', 'customer_id','stores','store_id','store','customer','digit','default_business_settings'));

            if($store_id == 'All'){

                $openingDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(2)
                                ->wherecustomer_id($customer_id)
                                ->where('date', $from)
                                ->where('order_type', 'Previous Due')
                                ->first();

                $openingDueBalance = PaymentReceipt::whereorder_type_id(2)
                                ->wherecustomer_id($customer_id)
                                ->where('date', '<', $from)
                                ->where(function ($query) {
                                    $query->whereorder_type('Sale')
                                        ->orWhere('order_type', 'Previous Due');
                                })
                                ->sum('amount');

                if($openingDueBalanceOnlyOpeningTime){
                    $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
                }

                $store = 'All';
                // $sales = Sale::wherecustomer_id($customer_id)->whereBetween('voucher_date', array($from, $to))->get();
                // $payments = PaymentReceipt::wherecustomer_id($customer_id)
                // ->where('order_type','Received')
                // ->where('order_type_id',1)
                // ->whereBetween('date', array($from, $to))
                // ->get();

                $sales = PaymentReceipt::wherecustomer_id($customer_id)
                ->whereBetween('date', array($from, $to))
                ->where(function ($query) {
                    $query->whereorder_type('Sale')
                        ->orWhere('order_type', 'Received')
                        ->orWhere('order_type', 'Previous Due');
                })
                ->orderBy('date','asc')
                ->get();

                $saleReturns = SaleReturn::wherecustomer_id($customer_id)->whereBetween('return_date', array($from, $to))->get();
                $dues = PaymentReceipt::wherecustomer_id($customer_id)->where('order_type','Sale')->where('order_type_id',2)->whereBetween('date', array($from, $to))->get();



            }else{
                $openingDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
                                ->wherecustomer_id($customer_id)
                                ->where('date', $from)
                                ->where('order_type', 'Previous Due')
                                ->first();

                $openingDueBalance = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
                                ->wherecustomer_id($customer_id)
                                ->where('date', '<', $from)
                                ->where(function ($query) {
                                    $query->Where('order_type', 'Previous Due');
                                })
                                ->sum('amount');

                if($openingDueBalanceOnlyOpeningTime){
                    $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
                }
                $store = Store::find($store_id);
                // $sales = Sale::wherecustomer_id($customer_id)->where('store_id', '=', $store_id)->whereBetween('voucher_date', array($from, $to))->get();
                // $saleReturns = SaleReturn::wherecustomer_id($customer_id)->where('store_id', '=', $store_id)->whereBetween('return_date', array($from, $to))->get();
                $sales = PaymentReceipt::wherecustomer_id($customer_id)
                ->where('store_id', '=', $store_id)
                ->whereBetween('date', array($from, $to))
                ->where(function ($query) {
                    $query->whereorder_type('Sale')
                        ->orWhere('order_type', 'Received')
                        ->orWhere('order_type', 'Previous Due');
                })
                ->orderBy('date','asc')
                ->get();
                $saleReturns = SaleReturn::wherecustomer_id($customer_id)->where('store_id', '=', $store_id)->whereBetween('return_date', array($from, $to))->get();
                $dues = PaymentReceipt::wherecustomer_id($customer_id)->where('order_type','Sale')->where('order_type_id',2)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
                $payments = PaymentReceipt::wherecustomer_id($customer_id)->where('order_type','Received')->where('order_type_id',1)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
            }

            // $pdf = Pdf::loadView('backend.common.customer_ledgers.pdf_view_2', compact('sales', 'saleReturns', 'openingDueBalance','dues','payments','from', 'to', 'store', 'store_id', 'default_currency','default_business_settings','customer','customer_id'));
            $pdf = Pdf::loadView('backend.common.customer_ledgers.pdf_view_3', compact('sales', 'saleReturns', 'openingDueBalance','dues','from', 'to', 'store', 'store_id', 'default_currency','default_business_settings','customer','customer_id'));



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
