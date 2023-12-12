<?php

namespace App\Helpers;


use App\Models\OperationLog;
use App\Models\Module;
use App\Models\Store;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\AdvanceReceipt;
use App\Models\PurchaseReturnDetail;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleProduct;
use App\Models\SaleReturnDetail;
use App\Models\PurchaseReturn;
use App\Models\Unit;
use App\Models\PaymentType;
use App\Models\OrderType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Facades\Agent;


class Helper
{


    public static function make_slug($string)
    {
        return Str::slug($string, '-');
    }

    public static function getCollapseAndParentModuleList()
    {
        return Module::where('status', 1)
            ->where(function ($query) {
                $query
                    ->where('parent_menu', 'Collapse')
                    ->orWhere('parent_menu', 'Parent');
            })
            ->orderBy('serial', 'asc')
            ->get();
    }

    public static function getChildModuleList($parent)
    {
        return Module::where('parent_menu', $parent)
            ->where('status', 1)
            ->orderBy('serial', 'asc')
            ->get();
    }

    public static function getChildModuleSlugList($parent, $role)
    {
        $childModules = Module::where('parent_menu', $parent)
            ->where('status', 1)
            ->orderBy('serial', 'asc')
            ->get();
        $slugs = [];
        if (count($childModules) > 0) {
            foreach ($childModules as $key => $childModule) {
                $slugs[] = $childModule->slug;
            }
            $slugs_array = implode(',', $slugs);
            $slugList = explode(',', $slugs_array);
        } else {
            $slugList = [];
        }
        return $slugList;
    }

    public static function collapseChildMenuPermission($module_ids)
    {
        return DB::table('permissions')
            ->whereIn('module_id', $module_ids)
            ->pluck('name')
            ->first();
    }

    public static function getParentAndChildModuleList()
    {
        return Module::where('parent_menu', '!=', 'Collapse')
            ->where('status', 1)
            ->orderBy('serial', 'asc')
            ->get();
    }

    public static function getModulePermissionActionByModuleId($module_id)
    {
        return DB::table('permissions')
            ->where('module_id', $module_id)
            // ->orderBy('serial', 'asc')
            ->get();
    }

    public static function getStoreList($store_id)
    {
        if($store_id){
            return Store::where('status', 1)
            ->whereid($store_id)
            ->orderBy('id', 'asc')
            ->get();
        }else{
            return Store::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();
        }

    }

    public static function getSaleTotalAmount($id)
    {
        return Sale::where('id', $id)
            ->pluck('grand_total')
            ->first();
    }

    public static function getReportCount()
    {
        $reportCount = [
            'productCount' => Product::count(),
            'userCount' => User::count(),
            'customerCount' => Customer::count(),
            'supplierCount' => Supplier::count(),
        ];
        return $reportCount;
    }

    public static function getStoreReportCount($store_id)
    {
        $storeReportCount = [
            'purchaseAmount' => Purchase::wherestore_id($store_id)->sum('paid_amount'),
            'saleAmount' => Sale::wherestore_id($store_id)->sum('grand_total'),
            'purchaseReturnAmount' => PurchaseReturn::wherestore_id($store_id)->sum('total_buy_amount'),
            'saleReturnAmount' => 0,
        ];
        return $storeReportCount;
    }

    // public static function getCashPaidAmount($order_id,$store_id,$from,$to)
    // {
    //     if($store_id == 'All'){
    //         return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',1)->whereBetween('date', array($from, $to))->sum('amount');
    //     }else{
    //         return PaymentReceipt::where('order_id',$order_id)->wherestore_id($store_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',1)->whereBetween('date', array($from, $to))->sum('amount');
    //     }
    // }

    // public static function getMobileBankingPaidAmount($order_id,$store_id,$from,$to)
    // {
    //     if($store_id == 'All'){
    //         return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',2)->whereBetween('date', array($from, $to))->sum('amount');
    //     }else{
    //         return PaymentReceipt::where('order_id',$order_id)->wherestore_id($store_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',2)->whereBetween('date', array($from, $to))->sum('amount');
    //     }
    // }

    // public static function getOnlineBankPaidAmount($order_id,$store_id,$from,$to)
    // {
    //     if($store_id == 'All'){
    //         return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',3)->whereBetween('date', array($from, $to))->sum('amount');
    //     }else{
    //         return PaymentReceipt::where('order_id',$order_id)->wherestore_id($store_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',3)->whereBetween('date', array($from, $to))->sum('amount');
    //     }
    // }

    public static function getCashPaidAmount($order_id,$store_id,$date)
    {
        if($store_id == 'All'){
            return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',1)->where('receipt_time','Sale')->where('date', $date)->sum('amount');
        }else{
            return PaymentReceipt::where('order_id',$order_id)->wherestore_id($store_id)->where('order_type','Sale')->where('order_type_id',1)->where('receipt_time','Sale')->where('payment_type_id',1)->where('date', $date)->sum('amount');
        }
    }

    public static function getMobileBankingPaidAmount($order_id,$store_id,$date)
    {
        if($store_id == 'All'){
            return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',2)->where('receipt_time','Sale')->where('date', $date)->sum('amount');
        }else{
            return PaymentReceipt::where('order_id',$order_id)->wherestore_id($store_id)->where('order_type','Sale')->where('order_type_id',1)->where('receipt_time','Sale')->where('payment_type_id',2)->where('date', $date)->sum('amount');
        }
    }

    public static function getOnlineBankPaidAmount($order_id,$store_id,$date)
    {
        if($store_id == 'All'){
            return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('payment_type_id',5)->where('receipt_time','Sale')->where('date', $date)->sum('amount');
        }else{
            return PaymentReceipt::where('order_id',$order_id)->wherestore_id($store_id)->where('order_type','Sale')->where('order_type_id',1)->where('receipt_time','Sale')->where('payment_type_id',5)->where('date', $date)->sum('amount');
        }
    }

    public static function getUntilNowTotalPaidAmount($order_id,$created_at)
    {
        return PaymentReceipt::where('order_id',$order_id)->where('order_type','Sale')->where('order_type_id',1)->where('created_at', '<=',$created_at)->sum('amount');
    }

    public static function totalInvoiceAmount($store_id,$customer_id,$from,$to){
        $totalInvoiceAmount = PaymentReceipt::where('order_type','Sale')
                ->where('order_type_id',1)
                ->whereBetween('date', array($from, $to));
                if($store_id != 'All'){
                    $totalInvoiceAmount->wherestore_id($store_id);
                }
                if($customer_id){
                    $totalInvoiceAmount->wherecustomer_id($customer_id);
                }
                $infos = $totalInvoiceAmount->groupBy('order_id')
                ->select('order_id')
                ->get();

                $amount = 0;

                if(count($infos) > 0){
                    foreach($infos as $info){
                        $amount += Sale::where('id',$info->order_id)->pluck('grand_total')->first();
                    }
                }
                return $amount;
    }

    public static function totalInvoiceDueAmount($store_id,$customer_id,$from,$to){
        $totalInvoiceAmount = PaymentReceipt::where('order_type','Sale')
                ->where('order_type_id',1)
                ->whereBetween('date', array($from, $to));
                // if($store_id != 'All'){
                //     $totalInvoiceAmount->wherestore_id($store_id);
                // }
                if($customer_id){
                    $totalInvoiceAmount->wherecustomer_id($customer_id);
                }
                $infos = $totalInvoiceAmount->groupBy('order_id')
                ->select('order_id')
                ->get();

                $amount = 0;

                if(count($infos) > 0){
                    // dd($infos);
                    foreach($infos as $info){
                        $amount += Sale::where('id',$info->order_id)->pluck('due_amount')->first();
                    }
                }
                return $amount;
    }

    public static function previousDueBalance($store_id,$customer_id,$from){

        $v_total_amount=0;
        $v_total_cash_paid_amount=0;
        $v_total_card_paid_amount=0;
        $v_total_online_paid_amount=0;
        $v_total_due_amount=0;

        $p_total_cash_paid_amount=0;
        $p_total_card_paid_amount=0;
        $p_total_online_paid_amount=0;

        $s_r_total_cash_paid_amount=0;
        $s_r_total_card_paid_amount=0;
        $s_r_total_online_paid_amount=0;

        $customer_due_merge = 0;

        $opening_balance = 0;
        $customer_due = 0;

        $opening_balance_data = PaymentReceipt::where('order_type','Previous Due')
        ->where('order_type_id',2)
        ->where('date','<',$from);
        if($customer_id){
            $opening_balance_data->wherecustomer_id($customer_id);
        }
        $opening_balance_dues = $opening_balance_data->pluck('amount')->first();
        if($opening_balance_dues){
            $opening_balance = $opening_balance_dues;
        }

        $sales_data = Sale::where('voucher_date','<',$from);
        if($customer_id){
            $sales_data->wherecustomer_id($customer_id);
        }
        $sales = $sales_data->get();

        $saleReturns_data = SaleReturn::where('return_date','<',$from);
        if($customer_id){
            $saleReturns_data->wherecustomer_id($customer_id);
        }
        $saleReturns = $saleReturns_data->get();

        $dues_data = PaymentReceipt::where('order_type','Sale')
        ->where('order_type_id',2)
        ->where('date','<',$from);
        if($customer_id){
            $dues_data->wherecustomer_id($customer_id);
        }
        $dues = $dues_data->get();

        $payments_data = PaymentReceipt::where('order_type','Received')->where('order_type_id',1)
        ->where('date','<',$from);
        if($customer_id){
            $payments_data->wherecustomer_id($customer_id);
        }
        $payments= $payments_data->get();


        if($sales->isNotEmpty()){
            foreach($sales as $sale){
                $getCashVoucherAmount = $sale->payment_type_id == 1 ? $sale->paid_amount : 0;
                $getMobileBankingVoucherAmount = $sale->payment_type_id == 2 || $sale->payment_type_id == 3 ? $sale->paid_amount : 0;
                $getOnlineBankVoucherAmount = $sale->payment_type_id == 5 ? $sale->paid_amount : 0;
                $v_total_amount += $sale->grand_total;
                $v_total_cash_paid_amount += $getCashVoucherAmount;
                $v_total_card_paid_amount += $getMobileBankingVoucherAmount;
                $v_total_online_paid_amount += $getOnlineBankVoucherAmount;
                $v_total_due_amount += $sale->due_amount;
            }
        }

        if($saleReturns->isNotEmpty()){
            foreach ($saleReturns as $saleReturn){
                $getCashPaidAmountSaleReturn = $saleReturn->payment_type_id == 1 ? $saleReturn->refund_amount : 0;
                $getMobileBankingPaidAmountSaleReturn = $saleReturn->payment_type_id == 2 || $saleReturn->payment_type_id == 3 ? $saleReturn->refund_amount : 0;
                $getOnlineBankPaidAmountSaleReturn = $saleReturn->payment_type_id == 5 ? $saleReturn->refund_amount : 0;
                $s_r_total_cash_paid_amount += $getCashPaidAmountSaleReturn;
                $s_r_total_card_paid_amount += $getMobileBankingPaidAmountSaleReturn;
                $s_r_total_online_paid_amount += $getOnlineBankPaidAmountSaleReturn;
                $sale_return_grand_total = $saleReturn->grand_total;
                $total_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
                $customer_due_merge +=  $saleReturn->customer_due + $total_refund_amount;
            }
        }

        if($payments->isNotEmpty()){
            foreach ($payments as $payment){
                $getCashPaidAmount = $payment->payment_type_id == 1 ? $payment->amount : 0;
                $getMobileBankingPaidAmount = $payment->payment_type_id == 2 || $payment->payment_type_id == 3 ? $payment->amount : 0;
                $getOnlineBankPaidAmount = $payment->payment_type_id == 5 ? $payment->amount : 0;
                $p_total_cash_paid_amount += $getCashPaidAmount;
                $p_total_card_paid_amount += $getMobileBankingPaidAmount;
                $p_total_online_paid_amount += $getOnlineBankPaidAmount;
            }
        }

        $total_cash_paid_amount = $v_total_cash_paid_amount + $p_total_cash_paid_amount;
        $total_card_paid_amount = $v_total_card_paid_amount + $p_total_card_paid_amount;
        $total_online_paid_amount = $v_total_online_paid_amount + $p_total_online_paid_amount;
        $grand_total_cash_paid_amount = $total_cash_paid_amount - $s_r_total_cash_paid_amount;
        $grand_total_card_paid_amount = $total_card_paid_amount - $s_r_total_card_paid_amount;
        $grand_total_online_paid_amount = $total_online_paid_amount - $s_r_total_online_paid_amount;
        $grand_total_paid_amount = $grand_total_cash_paid_amount + $grand_total_card_paid_amount + $grand_total_online_paid_amount;
        $grand_total_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
        // $due_amount = ($v_total_amount - $grand_total_paid_amount) - $grand_total_refund_amount;
        return $due_amount = ($opening_balance + $v_total_amount) - $grand_total_paid_amount;
    }

    public static function allCustomerLedger($store_id,$customer_id){

        $v_total_amount=0;
        $v_total_cash_paid_amount=0;
        $v_total_card_paid_amount=0;
        $v_total_online_paid_amount=0;
        $v_total_due_amount=0;

        $p_total_cash_paid_amount=0;
        $p_total_card_paid_amount=0;
        $p_total_online_paid_amount=0;

        $total_cash_paid_amount = 0;
        $total_card_paid_amount = 0;
        $total_online_paid_amount = 0;
        $total_due_amount = 0;

        $s_r_total_refundable_amount=0;
        $s_r_total_cash_paid_amount=0;
        $s_r_total_card_paid_amount=0;
        $s_r_total_online_paid_amount=0;

        $customer_due_merge = 0;

        $opening_balance = 0;

        $data = [
            'total_amount' => 0,
            'total_paid_amount' => 0,
            'total_due_amount' => 0,
        ];

        // $customerOpeningBalance = Customer::where('id', $customer_id)->select('opening_balance')->first();
        $customerOpeningBalance = PaymentReceipt::wherecustomer_id($customer_id)->where('order_type','Previous Due')
        ->where('order_type_id',2)->pluck('amount')->first();
        if($customerOpeningBalance){
            // $opening_balance =$customerOpeningBalance->opening_balance;
            $opening_balance = $customerOpeningBalance;
        }

        if($customer_id){
        //    $sales = Sale::wherecustomer_id($customer_id)->get();
           $sales = PaymentReceipt::wherecustomer_id($customer_id)
                // ->where('store_id', '=', $store_id)
                // ->whereBetween('date', array($from, $to))
                ->where(function ($query) {
                    $query->whereorder_type('Sale')
                        ->orWhere('order_type', 'Received')
                        ->orWhere('order_type', 'Previous Due');
                })
                ->get();
        }else{
            // $sales = Sale::get();
            $sales = PaymentReceipt::where(function ($query) {
                    $query->whereorder_type('Sale')
                        ->orWhere('order_type', 'Received')
                        ->orWhere('order_type', 'Previous Due');
                })
                ->get();
        }

        if($customer_id){
            $saleReturns = SaleReturn::wherecustomer_id($customer_id)->get();
        }else{
            $saleReturns = SaleReturn::get();
        }

        $payments_data = PaymentReceipt::where('order_type','Received')
        ->where('order_type_id',1);
        if($customer_id){
            $payments_data->wherecustomer_id($customer_id);
        }
        $payments= $payments_data->get();


        if($sales->isNotEmpty()){
            foreach($sales as $sale){
                $getCashVoucherAmount = $sale->payment_type_id == 1 ? $sale->amount : 0;
                $getMobileBankingVoucherAmount = $sale->payment_type_id == 2 || $sale->payment_type_id == 3 ? $sale->amount : 0;
                $getOnlineBankVoucherAmount = $sale->payment_type_id == 5 ? $sale->amount : 0;
                $v_total_amount += $sale->total;
                $v_total_cash_paid_amount += $getCashVoucherAmount;
                $v_total_card_paid_amount += $getMobileBankingVoucherAmount;
                $v_total_online_paid_amount += $getOnlineBankVoucherAmount;
                $v_total_due_amount += $sale->due;
            }
        }

        // $advanceReceiptAmount = Helper::advanceReceiptAmount($store_id,$customer_id,'2023-04-01',date('Y-m-d'));
        $advanceReceiptAmount = AdvanceReceipt::wherecustomer_id($customer_id)->get();
        $advance_receipt_amount = 0;

        if($advanceReceiptAmount){
            foreach ($advanceReceiptAmount as $data){
                if(@$data->type == 'Advance Minus'){
                    $advance_receipt_amount += $data->amount;
                }
            }
        }

        if($saleReturns->isNotEmpty()){
            foreach ($saleReturns as $saleReturn){
                $s_r_total_refundable_amount += $saleReturn->refundable_amount;
                $getCashPaidAmountSaleReturn = $saleReturn->payment_type_id == 1 ? $saleReturn->refund_amount : 0;
                $getMobileBankingPaidAmountSaleReturn = $saleReturn->payment_type_id == 2 || $saleReturn->payment_type_id == 3 ? $saleReturn->refund_amount : 0;
                $getOnlineBankPaidAmountSaleReturn = $saleReturn->payment_type_id == 5 ? $saleReturn->refund_amount : 0;
                $s_r_total_cash_paid_amount += $getCashPaidAmountSaleReturn;
                $s_r_total_card_paid_amount += $getMobileBankingPaidAmountSaleReturn;
                $s_r_total_online_paid_amount += $getOnlineBankPaidAmountSaleReturn;
                $sale_return_grand_total = $saleReturn->grand_total;
                $total_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
                $customer_due_merge +=  $saleReturn->customer_due;
            }
        }

        // if($payments->isNotEmpty()){
        //     foreach ($payments as $payment){
        //         $getCashPaidAmount = $payment->payment_type_id == 1 ? $payment->amount : 0;
        //         $getMobileBankingPaidAmount = $payment->payment_type_id == 2 || $sale->payment_type_id == 3 ? $payment->amount : 0;
        //         $getOnlineBankPaidAmount = $payment->payment_type_id == 5 ? $payment->amount : 0;
        //         $p_total_cash_paid_amount += $getCashPaidAmount;
        //         $p_total_card_paid_amount += $getMobileBankingPaidAmount;
        //         $p_total_online_paid_amount += $getOnlineBankPaidAmount;
        //     }
        // }

        $total_cash_paid_amount = $v_total_cash_paid_amount + $p_total_cash_paid_amount;
        $total_card_paid_amount = $v_total_card_paid_amount + $p_total_card_paid_amount;
        $total_online_paid_amount = $v_total_online_paid_amount + $p_total_online_paid_amount;
        $grand_total_cash_paid_amount = $total_cash_paid_amount - $s_r_total_cash_paid_amount;
        $grand_total_card_paid_amount = $total_card_paid_amount - $s_r_total_card_paid_amount;
        $grand_total_online_paid_amount = $total_online_paid_amount - $s_r_total_online_paid_amount;
        $grand_total_paid_amount = $grand_total_cash_paid_amount + $grand_total_card_paid_amount + $grand_total_online_paid_amount;
        $grand_total_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
        $due_amount = ($v_total_amount - $grand_total_paid_amount) - $grand_total_refund_amount;

        $data['total_amount'] = $v_total_amount;
        $data['total_paid_amount'] = $grand_total_paid_amount;
        // $data['total_due_amount'] = $due_amount;
        $data['total_due_amount'] = ($due_amount + $opening_balance) - ($s_r_total_refundable_amount + $advance_receipt_amount);
        return $data;

    }


    public static function customerOpeningBalance($store_id,$customer_id,$from,$to){
        return Customer::whereBetween('start_date', array($from, $to))->where('id', $customer_id)->select('start_date','opening_balance')->first();
    }

    public static function allCustomerOpeningBalance($store_id){
        return Customer::where('opening_balance', '>', 0)->select('name','phone','start_date','opening_balance')->get();
    }

    public static function allCustomerOpeningBalanceBalanceSheet($store_id,$from,$to){
        return Customer::whereBetween('start_date', array($from, $to))->where('opening_balance', '>', 0)->select('name','phone','start_date','opening_balance')->get();
    }

    public static function customerDueAmount($store_id,$customer_id){
        $openingDueBalance = PaymentReceipt::whereorder_type_id(2)
            ->wherecustomer_id($customer_id)
            ->where(function ($query) {
                $query->whereorder_type('Sale')
                    ->orWhere('order_type', 'Previous Due');
            })
            ->sum('amount');

        $sales = PaymentReceipt::wherecustomer_id($customer_id)
        ->where(function ($query) {
            $query->whereorder_type('Sale')
                ->orWhere('order_type', 'Received')
                ->orWhere('order_type', 'Previous Due');
        })
        ->orderBy('date','asc')
        ->get();

        $saleReturns = SaleReturn::wherecustomer_id($customer_id)->get();
        $dues = PaymentReceipt::wherecustomer_id($customer_id)->where('order_type','Sale')->where('order_type_id',2)->get();

        $v_total_amount=0;
        $v_total_cash_paid_amount=0;
        $v_total_card_paid_amount=0;
        $v_total_online_paid_amount=0;
        $v_total_due_amount=0;
        $current_date_range_previous_due_amount=0;
        $advance_minus_amount=0;
        $p_total_cash_paid_amount=0;
        $p_total_card_paid_amount=0;
        $p_total_online_paid_amount=0;
        $total_cash_paid_amount = 0;
        $total_card_paid_amount = 0;
        $total_online_paid_amount = 0;
        $total_due_amount = 0;
        $s_r_total_refundable_amount=0;
        $s_r_total_cash_paid_amount=0;
        $s_r_total_card_paid_amount=0;
        $s_r_total_online_paid_amount=0;
        $advance_receipt_amount = 0;

        // $advanceReceiptAmount = Helper::advanceReceiptAmount($store_id,$customer_id,'2023-04-01',date('Y-m-d'));
        $advanceReceiptAmount = AdvanceReceipt::wherecustomer_id($customer_id)->get();
        if($advanceReceiptAmount){
            foreach ($advanceReceiptAmount as $data){
                if(@$data->type == 'Advance Minus'){
                    $advance_receipt_amount += $data->amount;
                }
            }
        }

        $opening_balance = 0;
        $customerOpeningBalance = Helper::customerOpeningBalance($store_id,$customer_id,'2023-04-01',date('Y-m-d'));
        if($customerOpeningBalance){
            $opening_balance = $customerOpeningBalance->opening_balance;
        }
        if($sales->isNotEmpty()){
            foreach($sales as $key => $sale){
                $getCashVoucherAmount = $sale->payment_type_id == 1 || $sale->payment_type_id == 4 ? $sale->amount : 0;
                $getMobileBankingVoucherAmount = $sale->payment_type_id == 2 || $sale->payment_type_id == 3 ? $sale->amount : 0;
                $getOnlineBankVoucherAmount = $sale->payment_type_id == 5 ? $sale->amount : 0;
                $v_total_amount += $sale->total;
                $v_total_cash_paid_amount += $getCashVoucherAmount;
                $v_total_card_paid_amount += $getMobileBankingVoucherAmount;
                $v_total_online_paid_amount += $getOnlineBankVoucherAmount;
                $v_total_due_amount += $sale->due_amount;
                if(@$sale->order_type == 'Previous Due'){
                    $current_date_range_previous_due_amount += $sale->amount;
                }

                $total_cash_paid_amount = $v_total_cash_paid_amount + $p_total_cash_paid_amount;
                $total_card_paid_amount = $v_total_card_paid_amount + $p_total_card_paid_amount;
                $total_online_paid_amount = $v_total_online_paid_amount + $p_total_online_paid_amount;
                $total_due_amount = $v_total_amount - ($total_cash_paid_amount + $total_card_paid_amount + $total_online_paid_amount);
            }
        }

        if ($saleReturns->isNotEmpty()){
            foreach ($saleReturns as $saleReturn){
                $s_r_total_refundable_amount += $saleReturn->refundable_amount;
                $getCashPaidAmountSaleReturn = $saleReturn->payment_type_id == 1 ? $saleReturn->refund_amount : 0;
                $getMobileBankingPaidAmountSaleReturn = $saleReturn->payment_type_id == 2 ? $saleReturn->refund_amount : 0;
                $getOnlineBankPaidAmountSaleReturn = $saleReturn->payment_type_id == 5 ? $saleReturn->refund_amount : 0;
                $s_r_total_cash_paid_amount += $getCashPaidAmountSaleReturn;
                $s_r_total_card_paid_amount += $getMobileBankingPaidAmountSaleReturn;
                $s_r_total_online_paid_amount += $getOnlineBankPaidAmountSaleReturn;
            }
        }

        $grand_total_cash_paid_amount = $total_cash_paid_amount - $s_r_total_cash_paid_amount;
        $grand_toal_card_paid_amount = $total_card_paid_amount - $s_r_total_card_paid_amount;
        $grand_toal_online_paid_amount = $total_online_paid_amount - $s_r_total_online_paid_amount;
        $grand_toal_paid_amount = $grand_total_cash_paid_amount + $grand_toal_card_paid_amount + $grand_toal_online_paid_amount;
        $grand_toal_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
        $due_amount = ($v_total_amount - $grand_toal_paid_amount) - $grand_toal_refund_amount;

        return ($current_date_range_previous_due_amount + $due_amount) - ($s_r_total_refundable_amount + $advance_receipt_amount);
    }

    public static function supplierDueAmount($store_id,$supplier_id){
        $v_total_amount = Purchase::where('supplier_id', $supplier_id)->sum('grand_total');
        $v_paid_amount = Purchase::where('supplier_id', $supplier_id)->sum('paid_amount');
        $v_due_amount = Purchase::where('supplier_id', $supplier_id)->sum('due_amount');
        $p_paid_amount = PaymentReceipt::where('supplier_id', $supplier_id)->where('order_type_id', 1)->where('receipt_time', 'Paid Amount')->sum('amount');
        // return 200;
        return $v_total_amount - ($v_paid_amount + $p_paid_amount);

    }

    public static function advanceReceiptAmount($store_id,$customer_id,$from,$to){
        $advanceReceiptAmount = AdvanceReceipt::whereBetween('date', array($from, $to));
                if($store_id != 'All'){
                    $advanceReceiptAmount->wherestore_id($store_id);
                }
                if($customer_id){
                    $advanceReceiptAmount->wherecustomer_id($customer_id);
                }
                return $advanceReceiptAmount->get();


    }


    public static function getUnitName($id)
    {
        return Unit::where('id', $id)
            ->pluck('name')
            ->first();
    }



    public static function getStoreName($id)
    {
        return Store::where('id', $id)
            ->pluck('name')
            ->first();
    }

    public static function getProductName($id)
    {
        return Product::where('id', $id)
            ->pluck('name')
            ->first();
    }

    public static function getPaymentTypeName($id)
    {
        return PaymentType::where('id', $id)
            ->pluck('name')
            ->first();
    }

    public static function getPaymentTypeId($id)
    {
        return PaymentReceipt::where('order_id', $id)
            ->pluck('payment_type_id')
            ->first();
    }

    public static function getOrderTypeName($id)
    {
        return OrderType::where('id', $id)
            ->pluck('name')
            ->first();
    }

    public static function getOperatorName($id)
    {
        return User::where('id', $id)
            ->pluck('name')
            ->first();
    }

    public static function getAlreadySaleReturnQty($sale_id,$product_id)
    {
        return SaleProduct::wheresale_id($sale_id)->whereproduct_id($product_id)->pluck('already_return_qty')->first();
    }

    public static function getSalePaymentInfo($sale_id)
    {
        return PaymentReceipt::whereorder_id($sale_id)->whereorder_type_id(1)->where('payment_type_id','!=',NULL)->whereorder_type('Sale')->get();
    }

    public static function getPurchasePaymentInfo($purchase_id)
    {
        return PaymentReceipt::whereorder_id($purchase_id)->whereorder_type_id(1)->where('payment_type_id','!=',NULL)->whereorder_type('Purchase')->get();
    }

    public static function ledgerCurrentBalance($ledgers)
    {
        $balance = 0;
        foreach ($ledgers as $data) {
            $amount = $data->amount;
            if ($data->order_type_id == 2) {
                $balance += $amount;
            }
        }
        return $balance;
    }

    public static function storeProductCurrentStock($store_id, $product_id)
    {
        // stock
        $total_purchase_qty = Stock::wherestore_id($store_id)->whereproduct_id($product_id)->sum('qty');
        // purchase return
        $total_purchase_return_qty = PurchaseReturnDetail::wherestore_id($store_id)->whereproduct_id($product_id)->sum('qty');
        // sale product
        $total_product_sale_qty = SaleProduct::wherestore_id($store_id)->whereproduct_id($product_id)->sum('qty');
        // sale package
        $total_package_sale_qty = 0;
        // sale return
        $total_sale_return_qty = SaleReturnDetail::wherestore_id($store_id)->whereproduct_id($product_id)->sum('qty');

        $total_sale_qty=$total_product_sale_qty+ $total_package_sale_qty;
        $purchase_sale_return_qty=($total_sale_qty-$total_sale_return_qty)+$total_purchase_return_qty;
        $current_stock = ($total_purchase_qty-$purchase_sale_return_qty);
        return $current_stock;
    }

    public static function operationLog($store_id=NULL, $module=NULL, $action=NULL, $previous_data=NULL, $current_data=NULL, $status=NULL, $activity_id=NULL, $remarks=NULL)
    {
        // $ip = '162.159.24.227'; /* Static IP address */
        $ip = \Request::ip();
        $currentUserInfo = Location::get($ip);
        // echo $currentUserInfo->countryName;
        // echo $currentUserInfo->regionName;
        // echo $currentUserInfo->cityName;
        // echo $currentUserInfo->zipCode;
        // echo $currentUserInfo->latitude;
        // echo $currentUserInfo->longitude;
        // dd($currentUserInfo);

        $browser = Agent::browser();
        $version = Agent::version($browser);
        $device = Agent::device();
        $platform = Agent::platform();
        if (Agent::isMobile()) {
            $access_by_device = 'Mobile';
        }else if (Agent::isDesktop()) {
            $access_by_device = 'Desktop';
        }else if (Agent::isTablet()) {
            $access_by_device = 'Tablet';
        }else if (Agent::isPhone()) {
            $access_by_device = 'Phone';
        }
        // $device = $access_by_device.','.$platform.','.$browser.','.$version.','.$device;
        $device = $access_by_device.','.$platform.','.$browser.','.$version;

        $operationLog = new OperationLog();
        $operationLog->date = date('Y-m-d');
        $operationLog->user_id = Auth::user()->id;
        $operationLog->store_id = @$store_id;
        $operationLog->module = @$module;
        $operationLog->action = @$action;
        $operationLog->previous_data = json_encode(@$previous_data);
        $operationLog->current_data = json_encode(@$current_data);
        $operationLog->status = @$status;
        $operationLog->activity_id = @$activity_id;
        $operationLog->remarks = @$remarks;
        $operationLog->device = @$device;
        $operationLog->ip = @$ip;
        $operationLog->location = @$currentUserInfo->countryName;
        $operationLog->save();
    }

}
