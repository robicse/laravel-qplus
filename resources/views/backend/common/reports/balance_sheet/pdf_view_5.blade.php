<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balance Sheet Report </title>
    <style media="all">
        @font-face {
            font-family: 'zahidularabic';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url('{{ storage_path('fonts/Adobe_Arabic_Regular.ttf') }}');


        }

        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            color: #333542;
        }

        @page {
            size: A4;
            margin: 40px 20px !important;
        }

        body {
            font-size: .875rem;
            font-family: "dejavu sans mono, helvetica";
            color: #000000 !important
        }

        .arabic {
            direction: inherit !important;
            font-family: "zahidularabic" !important
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .5rem .7rem;
        }

        table.padding td {
            padding: .7rem;
        }

        table.sm-padding td {
            padding: .2rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: .65rem;
        }

        .currency {}
    </style>
</head>
<body>
    <div>
        <div style="padding: 1.5rem;">
            @include('backend.common.reports.header')
            @include('backend.common.reports.report_date_time',['invoice_type'=>'Balance Sheet'])
            <table>
                <tr>
                    <td style="width: 33%;" class="text-left small">Start Date: {{ $from }}</td>
                    <td style="width: 33%;" class="text-left small">End Date: {{ $to }}</td>
                    <td style="width: 33%;" class="text-right small">&nbsp;</td>
                </tr>
            </table>
        </div>

        @php
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

            $s_r_total_cash_paid_amount=0;
            $s_r_total_card_paid_amount=0;
            $s_r_total_online_paid_amount=0;

            $p_total_cash_advance_receipt_amount=0;
            $p_total_card_advance_receipt_amount=0;
            $p_total_online_advance_receipt_amount=0;

            $customer_due_merge = 0;

            $total_opening_balance = 0;
            $allCustomerOpeningBalances = Helper::allCustomerOpeningBalanceBalanceSheet($store_id,$from,$to);
            if(count($allCustomerOpeningBalances) > 0){
                foreach($allCustomerOpeningBalances as $allCustomerOpeningBalance){
                    $total_opening_balance += $allCustomerOpeningBalance->opening_balance;
                }
            }
        @endphp

        {{-- @if(count($allCustomerOpeningBalances) > 0)
            <div style="padding: 1.5rem;">
                <h5>Opening Balance</h5>
                <table class="padding text-left small border-bottom">
                    <thead>
                        <tr class="strong" style="background: #eceff4;">
                            <th>Start Date</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Due Amount</th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                        @foreach($allCustomerOpeningBalances as $allCustomerOpeningBalance){
                            <tr style="text-align: center">
                                <td>{{ $allCustomerOpeningBalance->start_date }}</td>
                                <td>{{ $allCustomerOpeningBalance->name }}</td>
                                <td>{{ $allCustomerOpeningBalance->phone }}</td>
                                <td>{{ $allCustomerOpeningBalance->opening_balance }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif --}}

        <div style="padding: 1.5rem;">
            <h5>Voucher Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Contact NO</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-right">Cash Paid Amount</th>
                        <th class="text-right">Card Paid Amount</th>
                        <th class="text-right">Online Bank Paid Amount</th>
                        <th class="text-right">Due Amount</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($sales->isNotEmpty())
                        @foreach ($sales as $sale)
                            @php
                                $getCashVoucherAmount = $sale->payment_type_id == 1 ? $sale->paid_amount : 0;
                                $getMobileBankingVoucherAmount = $sale->payment_type_id == 2 || $sale->payment_type_id == 3 ? $sale->paid_amount : 0;
                                $getOnlineBankVoucherAmount = $sale->payment_type_id == 5 ? $sale->paid_amount : 0;
                                $v_total_amount += $sale->grand_total;
                                $v_total_cash_paid_amount += $getCashVoucherAmount;
				                $v_total_card_paid_amount += $getMobileBankingVoucherAmount;
				                $v_total_online_paid_amount += $getOnlineBankVoucherAmount;
				                $v_total_due_amount += $sale->due_amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$sale->id }}</td>
                                <td>{{ @$sale->customer->name }}</td>
                                <td>{{ @$sale->customer->phone }}</td>
                                <td class="text-right">{{ $sale->grand_total }}</td>
                                <td class="text-right">{{ $getCashVoucherAmount }}</td>
                                <td class="text-right">{{ $getMobileBankingVoucherAmount }}</td>
                                <td class="text-right">{{ $getOnlineBankVoucherAmount }}</td>
                                <td class="text-right">{{ $sale->due_amount }}</td>
                                <td>{{ $sale->comments }}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="4">&nbsp;</td>
                            <td class="text-right">{{ $v_total_amount }}</td>
                            <td class="text-right">{{ $v_total_cash_paid_amount }}</td>
                            <td class="text-right">{{ $v_total_card_paid_amount }}</td>
                            <td class="text-right">{{ $v_total_online_paid_amount }}</td>
                            <td class="text-right">{{ $v_total_due_amount }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    @else
                        {{-- <tr><td colspan="8" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>

        <div style="padding: 1.5rem;">
            <h5>Sale Return Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Contact NO</th>
                        <th>Total Amount</th>
                        <th>Due Amount Merge</th>
                        <th class="text-right">Cash Paid Amount</th>
                        <th class="text-right">Card Paid Amount</th>
                        <th class="text-right">Online Bank Paid Amount</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($saleReturns->isNotEmpty())
                        @foreach ($saleReturns as $saleReturn)
                            @php
                                $getCashPaidAmountSaleReturn = $saleReturn->payment_type_id == 1 ? $saleReturn->refund_amount : 0;
                                $getMobileBankingPaidAmountSaleReturn = $saleReturn->payment_type_id == 2 || $saleReturn->payment_type_id == 3 ? $saleReturn->refund_amount : 0;
                                $getOnlineBankPaidAmountSaleReturn = $saleReturn->payment_type_id == 5 ? $saleReturn->refund_amount : 0;
                                $s_r_total_cash_paid_amount += $getCashPaidAmountSaleReturn;
                                $s_r_total_card_paid_amount += $getMobileBankingPaidAmountSaleReturn;
                                $s_r_total_online_paid_amount += $getOnlineBankPaidAmountSaleReturn;
                                $sale_return_grand_total = $saleReturn->grand_total;
                                $total_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
                                $customer_due_merge +=  $saleReturn->customer_due + $total_refund_amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$saleReturn->id }}</td>
                                <td>{{ @$saleReturn->customer->name }}</td>
                                <td>{{ @$saleReturn->customer->phone }}</td>
                                <td class="text-right"> {{ $saleReturn->grand_total }}</td>
                                <td class="text-right"> {{ $saleReturn->customer_due }}</td>
                                <td class="text-right"> {{ $getCashPaidAmountSaleReturn }}</td>
                                <td class="text-right">{{ $getMobileBankingPaidAmountSaleReturn }}</td>
                                <td class="text-right">{{ $getOnlineBankPaidAmountSaleReturn }}</td>
                                <th>{{ $saleReturn->comments }}</th>
                            </tr>
                        @endforeach
                    @else
                        {{-- <tr><td colspan="8" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>

        <div style="padding: 1.5rem;">
            <h5>Payment List</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Contact NO</th>
                        <th class="text-right">Cash Paid Amount</th>
                        <th class="text-right">Card Paid Amount</th>
                        <th class="text-right">Online Bank Paid Amount</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($payments->isNotEmpty())
                        @foreach ($payments as $payment)
                            @php
                                $getCashPaidAmount = $payment->payment_type_id == 1 || $payment->payment_type_id == 4 ? $payment->amount : 0;
                                $getMobileBankingPaidAmount = $payment->payment_type_id == 2 || $payment->payment_type_id == 3 ? $payment->amount : 0;
                                $getOnlineBankPaidAmount = $payment->payment_type_id == 5 ? $payment->amount : 0;
                                $p_total_cash_paid_amount += $getCashPaidAmount;
                                $p_total_card_paid_amount += $getMobileBankingPaidAmount;
                                $p_total_online_paid_amount += $getOnlineBankPaidAmount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$payment->invoice_no }}</td>
                                <td>{{ @$payment->customer->name }}</td>
                                <td>{{ @$payment->customer->phone }}</td>
                                <td class="text-right">{{ $getCashPaidAmount }}</td>
                                <td class="text-right">{{ $getMobileBankingPaidAmount }}</td>
                                <td class="text-right">{{ $getOnlineBankPaidAmount }}</td>
                                <td>{{ $payment->comments }}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="4">&nbsp;</td>
                            <td class="text-right">{{ $p_total_cash_paid_amount }}</td>
                            <td class="text-right">{{ $p_total_card_paid_amount }}</td>
                            <td class="text-right">{{ $p_total_online_paid_amount }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    @else
                        {{-- <tr><td colspan="8" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>

        <div style="padding: 1.5rem;">
            <h5>Advance Receipt List</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Contact NO</th>
                        <th class="text-right">Cash Paid Amount</th>
                        <th class="text-right">Card Paid Amount</th>
                        <th class="text-right">Online Bank Paid Amount</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($advanceReceipts->isNotEmpty())
                        @foreach ($advanceReceipts as $advanceReceipt)
                            @php
                                $getCashAdvanceReceiptAmount = $advanceReceipt->payment_type_id == 1 ? $advanceReceipt->amount : 0;
                                $getMobileBankingAdvanceReceiptAmount = $advanceReceipt->payment_type_id == 2 || $advanceReceipt->payment_type_id == 3 ? $advanceReceipt->amount : 0;
                                $getOnlineBankAdvanceReceiptAmount = $advanceReceipt->payment_type_id == 5 ? $advanceReceipt->amount : 0;
                                $p_total_cash_advance_receipt_amount += $getCashAdvanceReceiptAmount;
                                $p_total_card_advance_receipt_amount += $getMobileBankingAdvanceReceiptAmount;
                                $p_total_online_advance_receipt_amount += $getOnlineBankAdvanceReceiptAmount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$advanceReceipt->invoice_no }}</td>
                                <td>{{ @$advanceReceipt->customer->name }}</td>
                                <td>{{ @$advanceReceipt->customer->phone }}</td>
                                <td class="text-right">{{ $getCashAdvanceReceiptAmount }}</td>
                                <td class="text-right">{{ $getMobileBankingAdvanceReceiptAmount }}</td>
                                <td class="text-right">{{ $getOnlineBankAdvanceReceiptAmount }}</td>
                                <td>{{ $advanceReceipt->comments }}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="4">&nbsp;</td>
                            <td class="text-right">{{ $p_total_cash_advance_receipt_amount }}</td>
                            <td class="text-right">{{ $p_total_card_advance_receipt_amount }}</td>
                            <td class="text-right">{{ $p_total_online_advance_receipt_amount }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    @else
                        {{-- <tr><td colspan="8" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>

    </div>

    @php
        $total_cash_paid_amount = $v_total_cash_paid_amount + $p_total_cash_paid_amount;
        $total_card_paid_amount = $v_total_card_paid_amount + $p_total_card_paid_amount;
        $total_online_paid_amount = $v_total_online_paid_amount + $p_total_online_paid_amount;
        $grand_total_cash_paid_amount = ($total_cash_paid_amount + $p_total_cash_advance_receipt_amount) - $s_r_total_cash_paid_amount;
        $grand_toal_card_paid_amount = ($total_card_paid_amount + $p_total_card_advance_receipt_amount) - $s_r_total_card_paid_amount;
        $grand_toal_online_paid_amount = ($total_online_paid_amount + $p_total_online_advance_receipt_amount) - $s_r_total_online_paid_amount;
        $grand_toal_paid_amount = $grand_total_cash_paid_amount + $grand_toal_card_paid_amount + $grand_toal_online_paid_amount;
        $grand_toal_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;
        $due_amount = ($v_total_amount - $grand_toal_paid_amount) - $customer_due_merge;
        $previousDueBalance = Helper::previousDueBalance(null,null,$from);
    @endphp

    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                {{-- <tr>
                    <th class="text-left strong">Balance B/F ({{ date('Y-m-d', strtotime($from. ' - 1 days')) }})</th>
                    <td class="currency">- {{ number_format($previousDueBalance,2) }}</td>
                </tr> --}}
                <tr>
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($v_total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Cash Paid Amount</th>
                    <td class="currency">{{ number_format($grand_total_cash_paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Card Paid Amount</th>
                    <td class="currency">{{ number_format($grand_toal_card_paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Online Paid Amount</th>
                    <td class="currency">{{ number_format($grand_toal_online_paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Due Amount</th>
                    {{-- <td class="currency">{{ number_format( ($due_amount + $total_opening_balance), 2) }}</td> --}}
                    <td class="currency">{{ number_format( ($v_total_due_amount + $total_opening_balance), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    </div>
</body>

</html>
