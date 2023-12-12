<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\RoleController;
use App\Http\Controllers\Common\UserController;
use App\Http\Controllers\Common\BusinessSettingController;
use App\Http\Controllers\Common\StoreController;
use App\Http\Controllers\Common\SubMenuController;
use App\Http\Controllers\Common\SupplierController;
use App\Http\Controllers\Common\CustomerController;
use App\Http\Controllers\Common\CategoryController;
use App\Http\Controllers\Common\UnitController;
use App\Http\Controllers\Common\ProductController;
use App\Http\Controllers\Common\PackageController;
use App\Http\Controllers\Common\PurchaseController;
use App\Http\Controllers\Common\StockController;
use App\Http\Controllers\Common\PurchaseReturnController;
use App\Http\Controllers\Common\SaleController;
use App\Http\Controllers\Common\SaleReturnController;
use App\Http\Controllers\Common\BlankSaleController;
use App\Http\Controllers\Common\AdvanceReceiptController;
use App\Http\Controllers\Common\CustomerReceiptController;
use App\Http\Controllers\Common\CustomerPaymentController;
use App\Http\Controllers\Common\CustomerDueController;
use App\Http\Controllers\Common\CustomerReturnDueController;
use App\Http\Controllers\Common\SupplierPaymentController;
use App\Http\Controllers\Common\SupplierDueController;
use App\Http\Controllers\Common\ReportController;
use App\Http\Controllers\Common\SupplierLedgerController;
use App\Http\Controllers\Common\CustomerLedgerController;
use App\Http\Controllers\Common\AllCustomerLedgerController;

// common controller
Route::post('update-status', [CommonController::class, 'updateStatus'])->name('updateStatus');
Route::get('purchase-relation-data',[CommonController::class, 'PurchaseRelationData']);
Route::get('sale-relation-data',[CommonController::class, 'SaleRelationData']);
Route::get('blank-sale-relation-data',[CommonController::class, 'BlankSaleRelationData']);
Route::get('sale-return-relation-data',[CommonController::class, 'SaleReturnRelationData']);
Route::post('find-product-info', [CommonController::class, 'FindProductInfo']);
Route::post('get-store-customer', [CommonController::class, 'FindCustomerInfo']);
Route::get('get-payment-list',[CommonController::class, 'PaymentList']);

Route::resource('roles', RoleController::class);
Route::resource('users', UserController::class);
Route::resource('business-settings', BusinessSettingController::class);
Route::post('/business-settings-update', [BusinessSettingController::class, 'businessSettingsUpdate'])->name('business.settings.update');
Route::resource('stores', StoreController::class);
Route::get('changepassword', [UserController::class, 'changepassword'])->name('changepassword');
Route::post('updatepassword', [UserController::class, 'updatepassword']);
Route::get('ban/{id}', [UserController::class, 'ban'])->name('ban');
Route::get('sub-menu/{id}', [SubMenuController::class, 'subMenuList']);
Route::resource('suppliers', SupplierController::class);
Route::resource('customers', CustomerController::class);
Route::resource('categories', CategoryController::class);
Route::resource('units', UnitController::class);
Route::resource('products', ProductController::class);
Route::resource('packages', PackageController::class);
Route::post('get-product-by-search', [PackageController::class, 'FindProductBySearchProductName']);
Route::get('/category-product-info', [PackageController::class, 'categoryProductInfo'])->name('category.product.info');
Route::resource('purchases', PurchaseController::class);
Route::get('purchases-prints/{id}/{pagesize}', [PurchaseController::class, 'purchasePrintWithPageSize']);
Route::get('/purchases-invoice-pdf/{id}', [PurchaseController::class, 'purchaseInvoicePdfDownload']);
Route::resource('stocks', StockController::class);
Route::resource('purchase-returns', PurchaseReturnController::class);
Route::resource('sales', SaleController::class);
Route::get('customer-advance-balance-info/{id}', [SaleController::class, 'customerAdvanceBalanceInfo']);
Route::get('sales-prints/{id}/{pagesize}', [SaleController::class, 'salePrintWithPageSize'])->name('sales-prints');
Route::get('/sales-invoice-pdf/{id}', [SaleController::class, 'saleInvoicePdfDownload']);
Route::resource('sale-returns', SaleReturnController::class);
Route::get('sale-returns-prints/{id}/{pagesize}', [SaleReturnController::class, 'saleReturnPrintWithPageSize'])->name('sale-returns-prints');
Route::get('/sale-returns-invoice-pdf/{id}', [SaleReturnController::class, 'saleReturnInvoicePdfDownload']);
Route::get('sale-info', [SaleReturnController::class, 'saleInfo'])->name('sale.info');
Route::resource('blank-sales', BlankSaleController::class);
Route::get('/blank-sales-invoice-pdf/{id}', [BlankSaleController::class, 'blankSaleInvoicePdfDownload']);
Route::resource('advance-receipts', AdvanceReceiptController::class);
Route::get('/advance-receipts-invoice-pdf/{id}', [AdvanceReceiptController::class, 'advanceReceiptsInvoicePdfDownload']);
Route::resource('customer-receipts', CustomerReceiptController::class);
Route::get('customer-receipts-prints/{id}/{pagesize}', [CustomerReceiptController::class, 'customerReceiptsPrintWithPageSize'])->name('customer-receipts-prints');
Route::get('/customer-receipts-invoice-pdf/{id}', [CustomerReceiptController::class, 'customerReceiptsInvoicePdfDownload']);
Route::get('customer-due-balance-info/{id}', [CustomerReceiptController::class, 'customerDueBalanceInfo']);
Route::get('customer-due-amount/{id}', [CustomerReceiptController::class, 'customerDueAmount']);
Route::resource('customer-dues', CustomerDueController::class);
Route::resource('customer-return-dues', CustomerReturnDueController::class);
// For Customer Return
Route::resource('customer-payments', CustomerPaymentController::class);
Route::get('customer-return-due-balance-info/{id}', [CustomerPaymentController::class, 'customerReturnDueBalanceInfo']);
Route::resource('supplier-payments', SupplierPaymentController::class);
Route::resource('supplier-dues', SupplierDueController::class);
Route::get('supplier-payments-prints/{id}/{pagesize}', [SupplierPaymentController::class, 'supplierPaymentsPrintWithPageSize'])->name('supplier-payments-prints');
Route::get('/supplier-payments-invoice-pdf/{id}', [SupplierPaymentController::class, 'supplierPaymentsInvoicePdfDownload']);
Route::get('supplier-due-balance-info/{id}', [SupplierPaymentController::class, 'supplierDueBalanceInfo']);
Route::get('supplier-due-amount/{id}', [SupplierPaymentController::class, 'supplierDueAmount']);

Route::get('purchase-store-wise-report', [ReportController::class, 'purchaseStoreWiseIndex'])->name('purchase-store-wise-report.index');
Route::post('purchase-store-wise-report', [ReportController::class, 'purchaseStoreWiseShow']);
Route::get('sale-store-wise-report', [ReportController::class, 'saleStoreWiseIndex'])->name('sale-store-wise-report.index');
Route::post('sale-store-wise-report', [ReportController::class, 'saleStoreWiseShow']);
Route::get('multiple-store-current-stock-report', [ReportController::class, 'MultipleStoreCurrentStockIndex'])->name('multiple-store-current-stock-report.index');
Route::post('multiple-store-current-stock-report', [ReportController::class, 'MultipleStoreCurrentStockShow']);
Route::resource('supplier-ledgers', SupplierLedgerController::class);
Route::resource('customer-ledgers', CustomerLedgerController::class);
Route::resource('all-customer-ledgers', AllCustomerLedgerController::class);
Route::get('loss-profits', [ReportController::class, 'lossProfitStoreWiseIndex'])->name('loss-profits.index');
Route::post('loss-profit-store-wise-report', [ReportController::class, 'lossProfitStoreWiseShow']);
Route::get('product-price-status', [ReportController::class, 'productPriceStatusIndex'])->name('product-price-status.index');
Route::post('product-price-status', [ReportController::class, 'productPriceStatusShow']);
Route::get('date-wise-sale', [ReportController::class, 'dateWiseSaleIndex'])->name('date-wise-sale.index');
Route::post('date-wise-sale', [ReportController::class, 'dateWiseSaleShow']);
Route::get('date-wise-voucher', [ReportController::class, 'dateWiseVoucherIndex'])->name('date-wise-voucher.index');
Route::get('date-wise-voucher-pdf/{start_date}/{end_date}/{store_id}/{previewtype}',[ReportController::class, 'dateWiseVoucherShow']);
// Route::post('date-wise-voucher', [ReportController::class, 'dateWiseVoucherShow']);
Route::get('today-voucher', [ReportController::class, 'todayVoucherIndex'])->name('today-voucher.index');
Route::post('today-voucher', [ReportController::class, 'todayVoucherShow']);
Route::get('balance-sheet', [ReportController::class, 'balanceSheetIndex'])->name('balance-sheet.index');
Route::post('balance-sheet', [ReportController::class, 'balanceSheetShow']);
Route::get('check-product-in-stock', [ReportController::class, 'checkProductInStockIndex'])->name('check-product-in-stock.index');
Route::get('stock-low-list', [ReportController::class, 'stockLowList'])->name('stock-lows.index');
Route::get('stock-low-list-details/{store_id}', [ReportController::class, 'stockLowListDEtails'])->name('stock.low.list.details');
Route::get('customer-last-product', [ReportController::class, 'customerLastProductIndex'])->name('customer-last-product.index');
