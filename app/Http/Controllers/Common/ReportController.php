<?php

namespace App\Http\Controllers\Common;
use App\Models\Van;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\SaleProduct;
use App\Models\SaleReturn;
use App\Models\PaymentReceipt;
use App\Models\AdvanceReceipt;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\CurrencyTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaleReturnCustomerToVanExport;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use App\Http\Traits\BusinessSettingTrait;
use App\Helpers\Helper;
use NumberFormatter;

class ReportController extends Controller
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
    }

    public function index()
    {
        //return view('backend.common.reports.index');
    }

    public function purchaseStoreWiseIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            $products = Product::wherestatus(1)->get();
            $suppliers = Supplier::wherestatus(1)->get();
            return view(
                'backend.common.reports.purchase_store_wise_report.index',
                compact('stores', 'products', 'suppliers', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function purchaseStoreWiseShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $from = date('Y-m-d', strtotime($request->start_date));
            $to = date('Y-m-d', strtotime($request->end_date));
            $store_id = $request->store_id;
            $supplier_id = $request->supplier_id;
            $product_id = $request->product_id;
            $storeInfo = Store::where('id', $store_id)->first();
            $previewtype = $request->previewtype;
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            $products = Product::wherestatus(1)->get();
            $suppliers = Supplier::wherestatus(1)->get();
            $report = Purchase::join(
                'stocks',
                'purchases.id',
                'stocks.purchase_id'
            )->whereBetween('purchase_date', [$from, $to]);
            if ($store_id == 'All') {
                $store = 'All';
            } else {
                $store = Store::find($store_id);
                $report->where('purchases.store_id', '=', $store_id);
            }

            if ($supplier_id == 'All') {
                $supplier = 'All';
            } else {
                $supplier = Supplier::find($supplier_id);
                $report->where('purchases.supplier_id', '=', $supplier_id);
            }

            if ($product_id == 'All') {
                $product = 'All';
            } else {
                $product = Supplier::find($product_id);
                $report->where('stocks.product_id', '=', $product_id);
            }
            $storeWisePurchaseReports = $report->get();
            $pdf = Pdf::loadView(
                'backend.common.reports.purchase_store_wise_report.pdf_view',
                compact(
                    'storeWisePurchaseReports',
                    'from',
                    'to',
                    'stores',
                    'store',
                    'store_id',
                    'suppliers',
                    'supplier',
                    'supplier_id',
                    'products',
                    'product',
                    'product_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
            return $pdf->stream('store_purchase_report_' . now() . '.pdf');

        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function saleStoreWiseIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view(
                'backend.common.reports.sale_store_wise_report.index',
                compact('stores', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function saleStoreWiseShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $from = date('Y-m-d', strtotime($request->start_date));
        $to = date('Y-m-d', strtotime($request->end_date));
        $store_id = $request->store_id;
        $storeInfo = Store::where('id', $store_id)->first();
        $previewtype = $request->previewtype;
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        if ($store_id == 'All') {
            $store = 'All';
            $storeWiseSaleReports = Sale::whereBetween('voucher_date', [
                $from,
                $to,
            ])->get();
        } else {
            $store = Store::find($store_id);
            $storeWiseSaleReports = Sale::where('store_id', '=', $store_id)
                ->whereBetween('voucher_date', [$from, $to])
                ->get();
        }

        if ($previewtype == 'htmlview') {
            return view(
                'backend.common.reports.sale_store_wise_report.reports',
                compact(
                    'storeWiseSaleReports',
                    'from',
                    'to',
                    'stores',
                    'store_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
        } elseif ($previewtype == 'pdfview') {
            $pdf = Pdf::loadView(
                'backend.common.reports.sale_store_wise_report.pdf_view',
                compact(
                    'storeWiseSaleReports',
                    'from',
                    'to',
                    'store',
                    'store_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
            return $pdf->stream('warehouse_sale_report_' . now() . '.pdf');
        } elseif ($previewtype == 'excelview') {
            return Excel::download(
                new SaleWarehouseWiseExport($storeWiseSaleReports, $storeInfo),
                now() . '_sale_warehouse_wise.xlsx'
            );
        } else {
            return view(
                'backend.common.reports.sale_store_wise_report.reports',
                compact(
                    'storeWiseSaleReports',
                    'from',
                    'to',
                    'stores',
                    'store_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
        }
    }

    public function MultipleStoreCurrentStockIndex()
    {
        try {
            $products = Product::wherestatus(1)->get();
            $stores = Store::wherestatus(1)->pluck('name', 'id');
            return view(
                'backend.common.reports.multiple_store_current_stock_reports.index',
                compact('stores', 'products')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }
    public function MultipleStoreCurrentStockShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();

        $stores = Store::wherestatus(1)->pluck('name', 'id');
        $store_ids = $request->store_id;
        $product_ids = $request->product_id;
        try {
            if (in_array('All', $product_ids)) {
                $product = 'All';
                $products = Product::wherestatus(1)->get();
            } else {
                $product = '';
                $products = Product::whereIn('id', $product_ids)->get();
            }

            return view(
                'backend.common.reports.multiple_store_current_stock_reports.reports',
                compact(
                    'stores',
                    'products',
                    'product',
                    'store_ids',
                    'product_ids',
                    'default_business_settings'
                )
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function lossProfitStoreWiseIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view(
                'backend.common.reports.loss_profit_store_wise_report.index',
                compact('stores', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function lossProfitStoreWiseShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $from = date('Y-m-d', strtotime($request->start_date));
        $to = date('Y-m-d', strtotime($request->end_date));
        $store_id = $request->store_id;
        $storeInfo = Store::where('id', $store_id)->first();
        $previewtype = $request->previewtype;
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }

        if ($store_id == 'All') {
            $store = 'All';
            $storeWiseLossProfitReports = Sale::whereBetween('voucher_date', [
                $from,
                $to,
            ])->get();
            $storeWiseLossProfitReportsReturn = SaleReturn::whereBetween(
                'return_date',
                [$from, $to]
            )->get();
        } else {
            $store = Store::find($store_id);
            $storeWiseLossProfitReports = Sale::where(
                'store_id',
                '=',
                $store_id
            )
                ->whereBetween('voucher_date', [$from, $to])
                ->get();
            $storeWiseLossProfitReportsReturn = SaleReturn::where(
                'store_id',
                '=',
                $store_id
            )
                ->whereBetween('return_date', [$from, $to])
                ->get();
        }

        if ($previewtype == 'htmlview') {
            return view(
                'backend.common.reports.loss_profit_store_wise_report.reports',
                compact(
                    'storeWiseLossProfitReports',
                    'storeWiseLossProfitReportsReturn',
                    'from',
                    'to',
                    'stores',
                    'store_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
        } elseif ($previewtype == 'pdfview') {
            $pdf = Pdf::loadView(
                'backend.common.reports.loss_profit_store_wise_report.pdf_view',
                compact(
                    'storeWiseLossProfitReports',
                    'storeWiseLossProfitReportsReturn',
                    'from',
                    'to',
                    'store',
                    'store_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
            return $pdf->stream('store_purchase_report_' . now() . '.pdf');
        } elseif ($previewtype == 'excelview') {
            return Excel::download(
                new SaleWarehouseWiseExport(
                    $storeWiseLossProfitReports,
                    $storeInfo
                ),
                now() . '_purchase_store_wise.xlsx'
            );
        } else {
            return view(
                'backend.common.reports.loss_profit_store_wise_report.reports',
                compact(
                    'storeWisePurchaseReports',
                    'storeWiseLossProfitReportsReturn',
                    'from',
                    'to',
                    'stores',
                    'store_id',
                    'storeInfo',
                    'previewtype',
                    'default_currency',
                    'default_business_settings'
                )
            );
        }
    }

    public function productPriceStatusIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            $products = Product::wherestatus(1)->get();
            return view(
                'backend.common.reports.product_price_status.index',
                compact('stores', 'products', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function productPriceStatusShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $from = date('Y-m-d', strtotime($request->start_date));
        $to = date('Y-m-d', strtotime($request->end_date));
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $storeInfo = Store::where('id', $store_id)->first();
        $previewtype = $request->previewtype;
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        if ($store_id == 'All') {
            $store = 'All';
            $productPriceStatusReports = Stock::join(
                'purchases',
                'stocks.purchase_id',
                'purchases.id'
            )
                ->select(
                    'purchases.purchase_date',
                    'purchases.store_id',
                    'stocks.product_id',
                    'stocks.purchase_price',
                    'stocks.sale_price'
                )
                ->where('stocks.product_id', '=', $product_id)
                ->whereBetween('purchase_date', [$from, $to])
                ->get();
        } else {
            $store = Store::find($store_id);
            $productPriceStatusReports = Stock::join(
                'purchases',
                'stocks.purchase_id',
                'purchases.id'
            )
                ->select(
                    'purchases.purchase_date',
                    'stocks.store_id',
                    'stocks.product_id',
                    'stocks.purchase_price',
                    'stocks.sale_price'
                )
                ->where('stocks.store_id', '=', $store_id)
                ->where('stocks.product_id', '=', $product_id)
                ->whereBetween('purchase_date', [$from, $to])
                ->get();
        }
        $pdf = Pdf::loadView(
            'backend.common.reports.product_price_status.pdf_view',
            compact(
                'productPriceStatusReports',
                'from',
                'to',
                'store',
                'store_id',
                'storeInfo',
                'previewtype',
                'default_currency',
                'default_business_settings'
            )
        );
        return $pdf->stream('product_price_status_report_' . now() . '.pdf');
    }

    public function dateWiseSaleIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            $products = Product::wherestatus(1)->get();
            return view(
                'backend.common.reports.product_price_status.index',
                compact('stores', 'products', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function dateWiseSaleShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $from = date('Y-m-d', strtotime($request->start_date));
        $to = date('Y-m-d', strtotime($request->end_date));
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $storeInfo = Store::where('id', $store_id)->first();
        $previewtype = $request->previewtype;
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        if ($store_id == 'All') {
            $store = 'All';
            $productPriceStatusReports = Stock::join(
                'purchases',
                'stocks.purchase_id',
                'purchases.id'
            )
                ->select(
                    'purchases.purchase_date',
                    'purchases.store_id',
                    'stocks.product_id',
                    'stocks.purchase_price',
                    'stocks.sale_price'
                )
                ->where('stocks.product_id', '=', $product_id)
                ->whereBetween('purchase_date', [$from, $to])
                ->get();
        } else {
            $store = Store::find($store_id);
            $productPriceStatusReports = Stock::join(
                'purchases',
                'stocks.purchase_id',
                'purchases.id'
            )
                ->select(
                    'purchases.purchase_date',
                    'stocks.store_id',
                    'stocks.product_id',
                    'stocks.purchase_price',
                    'stocks.sale_price'
                )
                ->where('stocks.store_id', '=', $store_id)
                ->where('stocks.product_id', '=', $product_id)
                ->whereBetween('purchase_date', [$from, $to])
                ->get();
        }
        $pdf = Pdf::loadView(
            'backend.common.reports.product_price_status.pdf_view',
            compact(
                'productPriceStatusReports',
                'from',
                'to',
                'store',
                'store_id',
                'storeInfo',
                'previewtype',
                'default_currency',
                'default_business_settings'
            )
        );
        return $pdf->stream('product_price_status_report_' . now() . '.pdf');
    }

    // public function dateWiseVoucherIndex()
    // {
    //     $default_currency = $this->getCurrencyInfoByDefaultCurrency();
    //     try {
    //         $stores = Store::wherestatus(1)->get();
    //         return view('backend.common.reports.date_wise_voucher.index', compact('stores', 'default_currency'));
    //     } catch (\Exception $e) {
    //         $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
    //         Toastr::error($response['message'], "Error");
    //         return back();
    //     }
    // }

    // public function dateWiseVoucherShow(Request $request)
    // {
    //     $default_business_settings = $this->getBusinessSettingsInfo();
    //     $default_currency = $this->getCurrencyInfoByDefaultCurrency();
    //     $from = date('Y-m-d', strtotime($request->start_date));
    //     $to = date('Y-m-d', strtotime($request->end_date));
    //     $store_id = $request->store_id;
    //     $storeInfo = Store::where('id', $store_id)->first();
    //     $previewtype = $request->previewtype;
    //     $stores = Store::wherestatus(1)->get();
    //     if($store_id == 'All'){
    //         $store = 'All';
    //         $dateWiseVoucherReports = Sale::whereBetween('voucher_date', array($from, $to))->get();
    //     }else{
    //         $store = Store::find($store_id);
    //         $dateWiseVoucherReports = Sale::whereBetween('voucher_date', array($from, $to))->where('stocks.store_id', '=', $store_id)->get();
    //     }
    //     $pdf = Pdf::loadView('backend.common.reports.date_wise_voucher.pdf_view', compact('dateWiseVoucherReports', 'from', 'to', 'store', 'store_id', 'storeInfo', 'previewtype', 'default_currency','default_business_settings'));
    //     return $pdf->stream('date_wise_voucher_report_' . now() . '.pdf');
    // }

    public function dateWiseVoucherIndex(Request $request)
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $start_date = $request->start_date ?? '';
            $end_date = $request->end_date ?? '';
            $store_id = $request->store_id ?? 'All';
            $previewtype = $request->previewtype ?? 'All';
            if ($store_id == 'All') {
                $store = 'All';
                $dateWiseVoucherReports = Sale::whereBetween('voucher_date', [
                    $start_date,
                    $end_date,
                ])->get();
            } else {
                $store = Store::find($store_id);
                $dateWiseVoucherReports = Sale::whereBetween('voucher_date', [
                    $start_date,
                    $end_date,
                ])
                    ->where('store_id', '=', $store_id)
                    ->get();
            }
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view(
                'backend.common.reports.date_wise_voucher.index',
                compact(
                    'stores',
                    'dateWiseVoucherReports',
                    'default_currency',
                    'start_date',
                    'end_date',
                    'store_id',
                    'previewtype'
                )
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function dateWiseVoucherShow(
        $start_date,
        $end_date,
        $store_id,
        $previewtype
    ) {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $storeInfo = Store::where('id', $store_id)->first();
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        if ($store_id == 'All') {
            $store = 'All';
            $dateWiseVoucherReports = Sale::whereBetween('voucher_date', [
                $start_date,
                $end_date,
            ])->get();
        } else {
            $store = Store::find($store_id);
            $dateWiseVoucherReports = Sale::whereBetween('voucher_date', [
                $start_date,
                $end_date,
            ])
                ->where('stocks.store_id', '=', $store_id)
                ->get();
        }
        $pdf = Pdf::loadView(
            'backend.common.reports.date_wise_voucher.pdf_view',
            compact(
                'dateWiseVoucherReports',
                'start_date',
                'end_date',
                'store',
                'store_id',
                'storeInfo',
                'previewtype',
                'default_currency',
                'default_business_settings'
            )
        );
        return $pdf->stream('date_wise_voucher_report_' . now() . '.pdf');
    }

    public function todayVoucherIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view(
                'backend.common.reports.today_voucher.index',
                compact('stores', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function todayVoucherShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $store_id = $request->store_id;
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        if ($store_id == 'All') {
            $store = 'All';
            $todayVoucherReports = Sale::where(
                'voucher_date',
                date('Y-m-d')
            )->get();
        } else {
            $store = Store::find($store_id);
            $todayVoucherReports = Sale::where('voucher_date', date('Y-m-d'))
                ->where('store_id', '=', $store_id)
                ->get();
        }
        $pdf = Pdf::loadView(
            'backend.common.reports.today_voucher.pdf_view',
            compact(
                'todayVoucherReports',
                'store',
                'store_id',
                'default_currency',
                'default_business_settings'
            )
        );
        return $pdf->stream('today_voucher_report_' . now() . '.pdf');
    }

    public function balanceSheetIndex()
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $User=$this->User;
                if ($User->user_type == 'Super Admin') {
                    $stores = Store::wherestatus(1)->get();
                }else{
                    $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
                }
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            return view(
                'backend.common.reports.balance_sheet.index',
                compact('stores', 'default_currency')
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function balanceSheetShow(Request $request)
    {
        $default_business_settings = $this->getBusinessSettingsInfo();
        $digit = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        $from = date('Y-m-d', strtotime($request->start_date));
        $to = date('Y-m-d', strtotime($request->end_date));
        $store_id = $request->store_id;
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $stores = Store::wherestatus(1)->get();
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }

        // $firstEntryDate = PaymentReceipt::pluck('date')->first();
        // if (empty($firstEntryDate)) {
        //     Toastr::warning('No Record Found!', 'Warning');
        //     return back();
        // }
        // if ($from < $firstEntryDate) {
        //     Toastr::warning(
        //         'You need to select date minimum:' . $firstEntryDate,
        //         'Warning'
        //     );
        //     return back();
        // }

        // for pdf_view_2

        // if($store_id == 'All'){
        //     $store = 'All';
        //     $sales = Sale::whereBetween('voucher_date', array($from, $to))->get();
        //     $saleReturns = SaleReturn::whereBetween('return_date', array($from, $to))->get();
        //     $dues = PaymentReceipt::where('order_type','Sale')->where('order_type_id',2)->whereBetween('date', array($from, $to))->get();
        //     $payments = PaymentReceipt::where('order_type','Sale')->where('order_type_id',1)->whereBetween('date', array($from, $to))->get();
        // }else{
        //     $store = Store::find($store_id);
        //     $sales = Sale::where('store_id', '=', $store_id)->whereBetween('voucher_date', array($from, $to))->get();
        //     $saleReturns = SaleReturn::where('store_id', '=', $store_id)->whereBetween('return_date', array($from, $to))->get();
        //     $dues = PaymentReceipt::where('order_type','Sale')->where('order_type_id',2)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
        //     $payments = PaymentReceipt::where('order_type','Sale')->where('order_type_id',1)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
        // }
        // // $pdf = Pdf::loadView('backend.common.reports.balance_sheet.pdf_view_1', compact('sales', 'saleReturns', 'dues','payments','from', 'to', 'store', 'store_id', 'default_currency','default_business_settings'));
        // $pdf = Pdf::loadView('backend.common.reports.balance_sheet.pdf_view_2', compact('sales', 'saleReturns', 'dues','payments','from', 'to', 'store', 'store_id', 'default_currency','default_business_settings'));

        // for pdf_view_4
        if($store_id == 'All'){
            $store = 'All';
            $sales = Sale::whereBetween('voucher_date', array($from, $to))->get();
            $payments = PaymentReceipt::where('order_type','Received')->where('order_type_id',1)->whereBetween('date', array($from, $to))->get();
            $saleReturns = SaleReturn::whereBetween('return_date', array($from, $to))->get();
            $dues = PaymentReceipt::where('order_type','Sale')->where('order_type_id',2)->whereBetween('date', array($from, $to))->get();
            $advanceReceipts = AdvanceReceipt::where('type','Advance')->where('payment_type_id',1)->whereBetween('date', array($from, $to))->get();
        }else{
            $store = Store::find($store_id);
            $sales = Sale::where('store_id', '=', $store_id)->whereBetween('voucher_date', array($from, $to))->get();
            $payments = PaymentReceipt::where('order_type','Received')->where('order_type_id',1)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
            $saleReturns = SaleReturn::where('store_id', '=', $store_id)->whereBetween('return_date', array($from, $to))->get();
            $dues = PaymentReceipt::where('order_type','Sale')->where('order_type_id',2)->where('store_id', '=', $store_id)->whereBetween('date', array($from, $to))->get();
            $advanceReceipts = AdvanceReceipt::where('store_id', '=', $store_id)->where('type','Advance')->where('payment_type_id',1)->whereBetween('date', array($from, $to))->get();
        }
        $pdf = Pdf::loadView('backend.common.reports.balance_sheet.pdf_view_5', compact('sales', 'saleReturns', 'dues','payments','from', 'to', 'store', 'store_id', 'default_currency','default_business_settings','advanceReceipts'));

        // for pdf_view_3
        // if($store_id == 'All'){
        //     $store = 'All';
        //     $balanceSheetReports = PaymentReceipt::whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale')
        //                                   ->orWhere('order_type', 'Sale Return')
        //                                   ->orWhere('order_type', 'Previous Due');
        //                         })
        //                         ->get();

        //     $openingDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(2)
        //                         ->where('date', $from)
        //                         ->where('order_type', 'Previous Due')
        //                         ->sum('amount');

        //     $openingDueBalance = PaymentReceipt::whereorder_type_id(2)
        //                         ->where('date', '<', $from)
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale')
        //                                 ->orWhere('order_type', 'Previous Due');
        //                         })
        //                         ->sum('amount');

        //         if($openingDueBalanceOnlyOpeningTime){
        //             $openingDueBalance += $openingDueBalanceOnlyOpeningTime;
        //         }

        //         $totalSaleAmount = PaymentReceipt::whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale');
        //                         })
        //                         ->sum('amount');

        //         $totalSaleReturnAmount = PaymentReceipt::whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale Return');
        //                         })
        //                         ->sum('amount');
        //         $totalAmount = $totalSaleAmount - $totalSaleReturnAmount;

        //         $paidAmount = PaymentReceipt::whereorder_type_id(1)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale')
        //                             ->orWhere('order_type', 'Previous Due');
        //                         })
        //                         ->sum('amount');

        //         $returnPaidAmount = PaymentReceipt::whereorder_type_id(1)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale Return');
        //                         })
        //                         ->sum('amount');
        //         $dueAmount = PaymentReceipt::whereorder_type_id(2)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale');
        //                         })
        //                         ->sum('amount');
        // }else{
        //     $store = Store::find($store_id);
        //     $balanceSheetReports = PaymentReceipt::wherestore_id($store_id)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale')
        //                                   ->orWhere('order_type', 'Sale Return')
        //                                   ->orWhere('order_type', 'Previous Due');
        //                         })
        //                         ->get();

        //     $openingDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
        //                         ->where('date', $from)
        //                         ->where('order_type', 'Previous Due')
        //                         ->sum('amount');

        //     $openingDueBalance = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
        //                         ->where('date', '<', $from)
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale')
        //                                 ->orWhere('order_type', 'Previous Due');
        //                         })
        //                         ->sum('amount');

        //         if($openingDueBalanceOnlyOpeningTime){
        //             $openingDueBalance += $openingDueBalanceOnlyOpeningTime;
        //         }

        //         $totalSaleAmount = PaymentReceipt::wherestore_id($store_id)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale');
        //                         })
        //                         ->sum('amount');
        //         $totalSaleReturnAmount = PaymentReceipt::wherestore_id($store_id)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale Return');
        //                         })
        //                         ->sum('amount');
        //         $totalAmount = $totalSaleAmount - $totalSaleReturnAmount;
        //         $paidAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(1)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale')
        //                             ->orWhere('order_type', 'Previous Due');
        //                         })
        //                         ->sum('amount');
        //         $returnPaidAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(1)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale Return');
        //                         })
        //                         ->sum('amount');
        //         $dueAmount = PaymentReceipt::wherestore_id($store_id)->whereorder_type_id(2)
        //                         ->whereBetween('date', array($from, $to))
        //                         ->where(function ($query) {
        //                             $query->whereorder_type('Sale');
        //                         })
        //                         ->sum('amount');
        // }

        // $pdf = Pdf::loadView('backend.common.reports.balance_sheet.pdf_view_3', compact('balanceSheetReports', 'openingDueBalance','totalAmount', 'paidAmount', 'returnPaidAmount','dueAmount', 'from', 'to', 'stores','store_id','store','digit','default_business_settings'));

        // for pdf_view_4
        // customer and sale
        // if ($store_id == 'All') {
        //     $store = 'All';
        //     $customerBalanceSheetReports = PaymentReceipt::whereBetween(
        //         'date',
        //         [$from, $to]
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Sale')
        //                 ->orWhere('order_type', 'Sale Return')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->get();

        //     $customerOpeningDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(
        //         2
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->where('date', $from)
        //         ->where('order_type', 'Previous Due')
        //         ->sum('amount');

        //     $customerOpeningDueBalance = PaymentReceipt::whereorder_type_id(2)
        //         ->where('customer_id', '!=', null)
        //         ->where('date', '<', $from)
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Sale')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');

        //     if ($customerOpeningDueBalanceOnlyOpeningTime) {
        //         $customerOpeningDueBalance += $customerOpeningDueBalanceOnlyOpeningTime;
        //     }

        //     $customerTotalSaleAmount = PaymentReceipt::whereBetween('date', [
        //         $from,
        //         $to,
        //     ])
        //         ->where('customer_id', '!=', null)
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale');
        //         })
        //         ->sum('amount');

        //     $customerTotalSaleReturnAmount = PaymentReceipt::whereBetween(
        //         'date',
        //         [$from, $to]
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale Return');
        //         })
        //         ->sum('amount');
        //     $customerTotalAmount =
        //         $customerTotalSaleAmount - $customerTotalSaleReturnAmount;

        //     $customerPaidAmount = PaymentReceipt::whereorder_type_id(1)
        //         ->where('customer_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Sale')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');

        //     $customerReturnPaidAmount = PaymentReceipt::whereorder_type_id(1)
        //         ->where('customer_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale Return');
        //         })
        //         ->sum('amount');
        //     $customerDueAmount = PaymentReceipt::whereorder_type_id(2)
        //         ->where('customer_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale');
        //         })
        //         ->sum('amount');
        // } else {
        //     $store = Store::find($store_id);
        //     $customerBalanceSheetReports = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Sale')
        //                 ->orWhere('order_type', 'Sale Return')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->get();

        //     $customerOpeningDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->whereorder_type_id(2)
        //         ->where('date', $from)
        //         ->where('order_type', 'Previous Due')
        //         ->sum('amount');

        //     $customerOpeningDueBalance = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->whereorder_type_id(2)
        //         ->where('date', '<', $from)
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Sale')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');

        //     if ($customerOpeningDueBalanceOnlyOpeningTime) {
        //         $customerOpeningDueBalance += $customerOpeningDueBalanceOnlyOpeningTime;
        //     }

        //     $customerTotalSaleAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('customer_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale');
        //         })
        //         ->sum('amount');
        //     $customerTotalSaleReturnAmount = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('customer_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale Return');
        //         })
        //         ->sum('amount');
        //     $customerTotalAmount =
        //         $customerTotalSaleAmount - $customerTotalSaleReturnAmount;
        //     $customerPaidAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('customer_id', '!=', null)
        //         ->whereorder_type_id(1)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Sale')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');
        //     $customerReturnPaidAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('customer_id', '!=', null)
        //         ->whereorder_type_id(1)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale Return');
        //         })
        //         ->sum('amount');
        //     $customerDueAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('customer_id', '!=', null)
        //         ->whereorder_type_id(2)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Sale');
        //         })
        //         ->sum('amount');
        // }

        // // supplier and Purchase
        // if ($store_id == 'All') {
        //     $store = 'All';
        //     $supplierBalanceSheetReports = PaymentReceipt::whereBetween(
        //         'date',
        //         [$from, $to]
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Purchase')
        //                 ->orWhere('order_type', 'Purchase Return')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->get();

        //     $supplierOpeningDueBalanceOnlyOpeningTime = PaymentReceipt::whereorder_type_id(
        //         2
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->where('date', $from)
        //         ->where('order_type', 'Previous Due')
        //         ->sum('amount');

        //     $supplierOpeningDueBalance = PaymentReceipt::whereorder_type_id(2)
        //         ->where('supplier_id', '!=', null)
        //         ->where('date', '<', $from)
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Purchase')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');

        //     if ($supplierOpeningDueBalanceOnlyOpeningTime) {
        //         $supplierOpeningDueBalance += $supplierOpeningDueBalanceOnlyOpeningTime;
        //     }

        //     $supplierTotalPurchaseAmount = PaymentReceipt::whereBetween(
        //         'date',
        //         [$from, $to]
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase');
        //         })
        //         ->sum('amount');

        //     $supplierTotalPurchaseReturnAmount = PaymentReceipt::whereBetween(
        //         'date',
        //         [$from, $to]
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase Return');
        //         })
        //         ->sum('amount');
        //     $supplierTotalAmount =
        //         $supplierTotalPurchaseAmount -
        //         $supplierTotalPurchaseReturnAmount;

        //     $supplierPaidAmount = PaymentReceipt::whereorder_type_id(1)
        //         ->where('supplier_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Purchase')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');

        //     $supplierReturnPaidAmount = PaymentReceipt::whereorder_type_id(1)
        //         ->where('supplier_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase Return');
        //         })
        //         ->sum('amount');
        //     $supplierDueAmount = PaymentReceipt::whereorder_type_id(2)
        //         ->where('supplier_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase')->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');
        // } else {
        //     $store = Store::find($store_id);
        //     $supplierBalanceSheetReports = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Purchase')
        //                 ->orWhere('order_type', 'Purchase Return')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->get();

        //     $supplierOpeningDueBalanceOnlyOpeningTime = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->whereorder_type_id(2)
        //         ->where('date', $from)
        //         ->where('order_type', 'Previous Due')
        //         ->sum('amount');

        //     $supplierOpeningDueBalance = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->whereorder_type_id(2)
        //         ->where('date', '<', $from)
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Purchase')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');

        //     if ($supplierOpeningDueBalanceOnlyOpeningTime) {
        //         $supplierOpeningDueBalance += $supplierOpeningDueBalanceOnlyOpeningTime;
        //     }

        //     $supplierTotalPurchaseAmount = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase');
        //         })
        //         ->sum('amount');
        //     $supplierTotalPurchaseReturnAmount = PaymentReceipt::wherestore_id(
        //         $store_id
        //     )
        //         ->where('supplier_id', '!=', null)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase Return');
        //         })
        //         ->sum('amount');
        //     $supplierTotalAmount =
        //         $supplierTotalPurchaseAmount -
        //         $supplierTotalPurchaseReturnAmount;
        //     $supplierPaidAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('supplier_id', '!=', null)
        //         ->whereorder_type_id(1)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query
        //                 ->whereorder_type('Purchase')
        //                 ->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');
        //     $supplierReturnPaidAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('supplier_id', '!=', null)
        //         ->whereorder_type_id(1)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase Return');
        //         })
        //         ->sum('amount');
        //     $supplierDueAmount = PaymentReceipt::wherestore_id($store_id)
        //         ->where('supplier_id', '!=', null)
        //         ->whereorder_type_id(2)
        //         ->whereBetween('date', [$from, $to])
        //         ->where(function ($query) {
        //             $query->whereorder_type('Purchase')->orWhere('order_type', 'Previous Due');
        //         })
        //         ->sum('amount');
        // }

        // $pdf = Pdf::loadView(
        //     'backend.common.reports.balance_sheet.pdf_view_4',
        //     compact(
        //         'customerBalanceSheetReports',
        //         'customerOpeningDueBalance',
        //         'customerTotalAmount',
        //         'customerPaidAmount',
        //         'customerReturnPaidAmount',
        //         'customerDueAmount',
        //         'supplierBalanceSheetReports',
        //         'supplierOpeningDueBalance',
        //         'supplierTotalAmount',
        //         'supplierPaidAmount',
        //         'supplierReturnPaidAmount',
        //         'supplierDueAmount',
        //         'from',
        //         'to',
        //         'stores',
        //         'store_id',
        //         'store',
        //         'digit',
        //         'default_business_settings'
        //     )
        // );
        return $pdf->stream('balance_sheet_' . now() . '.pdf');
    }

    public function stockLowList()
    {
        $stores = Store::all();
        return view('backend.common.stocks.stock_low', compact('stores'));
    }

    public function stockLowListDetails($store_id)
    {
        // $purchaseProducts = Purchase::join('stocks','purchases.id','stocks.purchase_id')
        // ->select('stocks.product_id')
        // ->where('purchases.store_id',$store_id)
        // ->groupBy('stocks.product_id')
        // ->get();

        $stock_lows = [];
        $products = Product::wherestatus(1)->get();
        if (count($products) > 0) {
            $nested_data = [];
            foreach ($products as $product) {
                $currentStoreProductCurrentStock = Helper::storeProductCurrentStock(
                    $store_id,
                    $product->id
                );
                if (
                    $currentStoreProductCurrentStock < $product->stock_low_qty
                ) {
                    $nested_data['store_id'] = $store_id;
                    $nested_data['store_name'] = Helper::getStoreName(
                        $store_id
                    );
                    $nested_data['product_id'] = $product->id;
                    $nested_data['product_name'] = $product->name;
                    $nested_data['stock_low_qty'] = $product->stock_low_qty;
                    $nested_data[
                        'current_stock_low_qty'
                    ] = $currentStoreProductCurrentStock;
                    array_push($stock_lows, $nested_data);
                }
            }
        }
        $stores = Store::all();
        return view(
            'backend.common.stocks.stock_low_details',
            compact('stores', 'stock_lows')
        );
    }

    public function checkProductInStockIndex(Request $request)
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $store_id = $request->store_id ?? 'All';
            $product_id = $request->product_id ?? '';

            if ($store_id == 'All') {
                $store = 'All';
                $total_purchase_qty = Stock::where(
                    'product_id',
                    $product_id
                )->sum('qty');
                $total_sale_qty = SaleProduct::where(
                    'product_id',
                    $product_id
                )->sum('qty');
            } else {
                $store = Store::find($store_id);
                $total_purchase_qty = Stock::where(
                    'product_id',
                    $product_id
                )->sum('qty');
                $total_sale_qty = SaleProduct::where('product_id', $product_id)
                    ->where('store_id', '=', $store_id)
                    ->sum('qty');
            }
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $User=$this->User;
                if ($User->user_type == 'Super Admin') {
                    $stores = Store::wherestatus(1)->get();
                }else{
                    $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
                }
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            $products = Product::wherestatus(1)->get();
            return view(
                'backend.common.reports.check_product_in_stock.index',
                compact(
                    'stores',
                    'products',
                    'total_purchase_qty',
                    'total_sale_qty',
                    'default_currency',
                    'store_id',
                    'product_id'
                )
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function customerLastProductIndex(Request $request)
    {
        $default_currency = $this->getCurrencyInfoByDefaultCurrency();
        try {
            $store_id = $request->store_id ?? 'All';
            $product_id = $request->product_id ?? '';
            $customer_id = $request->customer_id ?? '';

            if ($store_id == 'All') {
                $store = 'All';
                $sale_products = Sale::join(
                    'sale_products',
                    'sales.id',
                    'sale_products.sale_id'
                )
                    ->select(
                        'sales.id',
                        'sale_products.product_id',
                        'sale_products.qty',
                        'sale_products.sale_price'
                    )
                    ->where('sales.customer_id', $customer_id)
                    ->where('sale_products.product_id', $product_id)
                    ->get();
            } else {
                $store = Store::find($store_id);
                $sale_products = Sale::join(
                    'sale_products',
                    'sales.id',
                    'sale_products.sale_id'
                )
                    ->select(
                        'sales.id',
                        'sale_products.product_id',
                        'sale_products.qty',
                        'sale_products.sale_price'
                    )
                    ->where('sales.store_id', '=', $store_id)
                    ->where('sales.customer_id', $customer_id)
                    ->where('sale_products.product_id', $product_id)
                    ->get();
            }
            $User=$this->User;
            if ($User->user_type == 'Super Admin') {
                $User=$this->User;
                if ($User->user_type == 'Super Admin') {
                    $stores = Store::wherestatus(1)->get();
                }else{
                    $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
                }
            }else{
                $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
            }
            $products = Product::wherestatus(1)->get();
            $customers = Customer::wherestatus(1)->get();
            return view(
                'backend.common.reports.customer_last_product.index',
                compact(
                    'stores',
                    'products',
                    'customers',
                    'sale_products',
                    'default_currency',
                    'store_id',
                    'product_id',
                    'customer_id'
                )
            );
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }
}
