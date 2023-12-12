<?php

namespace App\Http\Controllers\Common;

use DB;
use DataTables;
use Carbon\Carbon;
use App\Models\PaymentReceipt;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Http\Traits\BusinessSettingTrait;
use NumberFormatter;

class SupplierLedgerController extends Controller
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
        // $this->middleware('permission:supplier-ledgers-list', ['only' => ['index', 'show']]);
        // $this->middleware('permission:supplier-ledgers-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:supplier-ledgers-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:supplier-ledgers-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        try {

            $suppliers = Supplier::wherestatus(1)->get();
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view('backend.common.supplier_ledgers.index', compact('suppliers','stores'));
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
            // dd($default_business_settings);
            $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
            $from = date('Y-m-d', strtotime($request->start_date));
            $to = date('Y-m-d', strtotime($request->end_date));
            $supplier_id = $request->supplier_id;
            $store_id = $request->store_id;
            $supplier = Supplier::find($supplier_id);
            $previewtype = $request->previewtype;
            $suppliers = Supplier::wherestatus(1)->get();
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            // if($store_id == 'All'){
            //     $store = 'All';
            //     $supplierReports = PaymentReceipt::whereorder_type('Purchase')->wheresupplier_id($supplier_id)->whereBetween('date', array($from, $to))->get();
            //     $preBalance = PaymentReceipt::whereorder_type('Purchase')->whereorder_type_id(2)->wheresupplier_id($supplier_id)->where('date', '<', $from)->sum('amount');
            // }else{
            //     $store = Store::find($store_id);
            //     $supplierReports = PaymentReceipt::whereorder_type('Purchase')->wherestore_id($store_id)->wheresupplier_id($supplier_id)->whereBetween('date', array($from, $to))->get();
            //     $preBalance = PaymentReceipt::whereorder_type('Purchase')->whereorder_type_id(2)->wherestore_id($store_id)->wheresupplier_id($supplier_id)->where('date', '<', $from)->sum('amount');
            // }
            // if ($previewtype == 'htmlview') {
            //     return view('backend.common.supplier_ledgers.reports', compact('supplierReports', 'preBalance', 'suppliers', 'from', 'to', 'supplier_id','stores','store_id','store','supplier','digit','default_business_settings'));
            // }else{
            //     $pdf = Pdf::loadView('backend.common.supplier_ledgers.pdf_view', compact('supplierReports', 'preBalance', 'suppliers', 'from', 'to', 'supplier_id','stores','store_id','store','supplier','digit','default_business_settings'));
            //     return $pdf->stream('store_purchase_report_' . now() . '.pdf');
            // }

            // $firstEntryDate= PaymentReceipt::wheresupplier_id($supplier_id)->pluck('date')->first();
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

            //     $supplierReports = PaymentReceipt::wheresupplier_id($supplier_id)
            //                         ->whereBetween('date', array($from, $to))
            //                         ->where(function ($query) {
            //                             $query->whereorder_type('Purchase')
            //                                 ->orWhere('order_type', 'Purchase Return')
            //                                 ->orWhere('order_type', 'Previous Due');
            //                         })
            //                         ->get();

            //     $openingDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(2)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->where('date', $from)
            //                     ->where('order_type', 'Previous Due')
            //                     ->first();

            //     $openingDueBalance = PaymentReceipt::whereorder_type_id(2)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->where('date', '<', $from)
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase')
            //                             ->orWhere('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');

            //     if($openingDueBalanceOnlyOpeningTime){
            //         $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
            //     }


            //     $totalSaleAmount = PaymentReceipt::wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase');
            //                     })
            //                     ->sum('amount');
            //     $totalSaleReturnAmount = PaymentReceipt::wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase Return');
            //                     })
            //                     ->sum('amount');

            //     $totalAmount = $totalSaleAmount;

            //     $paidAmount = PaymentReceipt::whereorder_type_id(1)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase')
            //                         ->orWhere('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');
            //     $returnPaidAmount = PaymentReceipt::whereorder_type_id(1)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase Return');
            //                     })
            //                     ->sum('amount');

            //     $dueAmount = PaymentReceipt::whereorder_type_id(2)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase');
            //                     })
            //                     ->sum('amount');
            // }else{
            //     $store = Store::find($store_id);

            //     $supplierReports = PaymentReceipt::wheresupplier_id($supplier_id)
            //                         ->wherestore_id($store_id)
            //                         ->whereBetween('date', array($from, $to))
            //                         ->where(function ($query) {
            //                             $query->whereorder_type('Purchase')
            //                                 ->orWhere('order_type', 'Purchase Return')
            //                                 ->orWhere('order_type', 'Previous Due');
            //                         })
            //                         ->get();

            //     $openingDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->where('date', $from)
            //                     ->where('order_type', 'Previous Due')
            //                     ->first();

            //     $openingDueBalance = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->where('date', '<', $from)
            //                     ->where(function ($query) {
            //                         $query->Where('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');

            //     if($openingDueBalanceOnlyOpeningTime){
            //         $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
            //     }

            //     $totalSaleAmount = PaymentReceipt::wherestore_id($store_id)->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase');
            //                     })
            //                     ->sum('amount');
            //     $totalSaleReturnAmount = PaymentReceipt::wherestore_id($store_id)->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase Return');
            //                     })
            //                     ->sum('amount');
            //     $totalAmount = $totalSaleAmount - $totalSaleReturnAmount;
            //     $paidAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(1)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase')
            //                         ->orWhere('order_type', 'Previous Due');
            //                     })
            //                     ->sum('amount');
            //     $returnPaidAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(1)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase Return');
            //                     })
            //                     ->sum('amount');
            //     $dueAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
            //                     ->wheresupplier_id($supplier_id)
            //                     ->whereBetween('date', array($from, $to))
            //                     ->where(function ($query) {
            //                         $query->whereorder_type('Purchase');
            //                     })
            //                     ->sum('amount');
            // }

            // $pdf = Pdf::loadView('backend.common.supplier_ledgers.pdf_view', compact('supplierReports', 'totalAmount', 'openingDueBalance', 'paidAmount', 'dueAmount', 'suppliers', 'from', 'to', 'supplier_id','stores','store_id','store','supplier','digit','default_business_settings'));

            if($store_id == 'All'){

                $openingDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(2)
                                ->wherecustomer_id($supplier_id)
                                ->where('date', $from)
                                ->where('order_type', 'Previous Due')
                                ->first();

                $openingDueBalance = PaymentReceipt::whereorder_type_id(2)
                                ->wherecustomer_id($supplier_id)
                                ->where('date', '<', $from)
                                ->where(function ($query) {
                                    $query->whereorder_type('Purchase')
                                        ->orWhere('order_type', 'Previous Due');
                                })
                                ->sum('amount');

                if($openingDueBalanceOnlyOpeningTime){
                    $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
                }

                $store = 'All';
                //$purchases = Purchase::wheresupplier_id($supplier_id)->whereBetween('purchase_date', array($from, $to))->get();
                // $payments = PaymentReceipt::wheresupplier_id($supplier_id)
                // ->where('order_type','Paid Amount')
                // ->where('order_type_id',1)
                // ->whereBetween('date', array($from, $to))
                // ->get();
                $purchases = PaymentReceipt::wheresupplier_id($supplier_id)
                ->whereBetween('date', array($from, $to))
                ->where(function ($query) {
                    $query->whereorder_type('Purchase')
                        ->orWhere('order_type', 'Paid Amount')
                        ->orWhere('order_type', 'Previous Due');
                })
                ->get();
                $purchaseReturns = PurchaseReturn::wheresupplier_id($supplier_id)->whereBetween('return_date', array($from, $to))->get();
                $dues = PaymentReceipt::wheresupplier_id($supplier_id)->where('order_type','Purchase')->where('order_type_id',2)->whereBetween('date', array($from, $to))->get();



            }else{
                $openingDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
                                ->wherecustomer_id($supplier_id)
                                ->where('date', $from)
                                ->where('order_type', 'Previous Due')
                                ->first();

                $openingDueBalance = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
                                ->wherecustomer_id($supplier_id)
                                ->where('date', '<', $from)
                                ->where(function ($query) {
                                    $query->Where('order_type', 'Previous Due');
                                })
                                ->sum('amount');

                if($openingDueBalanceOnlyOpeningTime){
                    $openingDueBalance += $openingDueBalanceOnlyOpeningTime->amount;
                }
                $store = Store::find($store_id);
                // $purchases = Purchase::wheresupplier_id($supplier_id)->where('store_id', '=', $store_id)->whereBetween('purchase_date', array($from, $to))->get();
                // $payments = PaymentReceipt::wheresupplier_id($supplier_id)->where('order_type','Paid Amount')->where('order_type_id',1)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
                $purchases = PaymentReceipt::wheresupplier_id($supplier_id)
                ->where('store_id', '=', $store_id)
                ->whereBetween('date', array($from, $to))
                ->where(function ($query) {
                    $query->whereorder_type('Purchase')
                        ->orWhere('order_type', 'Paid Amount')
                        ->orWhere('order_type', 'Previous Due');
                })
                ->get();
                $purchaseReturns = PurchaseReturn::wheresupplier_id($supplier_id)->where('store_id', '=', $store_id)->whereBetween('return_date', array($from, $to))->get();
                $dues = PaymentReceipt::wheresupplier_id($supplier_id)->where('store_id', '=', $store_id)->where('order_type','Purchase')->where('order_type_id',2)->whereBetween('date', array($from, $to))->get();

            }

            // $pdf = Pdf::loadView('backend.common.customer_ledgers.pdf_view_2', compact('sales', 'saleReturns', 'openingDueBalance','dues','payments','from', 'to', 'store', 'store_id', 'default_currency','default_business_settings','customer','supplier_id'));
            $pdf = Pdf::loadView('backend.common.supplier_ledgers.pdf_view_3', compact('purchases', 'purchaseReturns', 'openingDueBalance','dues','from', 'to', 'store', 'store_id', 'default_business_settings','supplier','supplier_id'));
            return $pdf->stream('supplier_ledger_' . now() . '.pdf');
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
