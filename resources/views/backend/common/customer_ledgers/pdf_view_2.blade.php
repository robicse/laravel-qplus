<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Ledger Report </title>
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
            @include('backend.common.reports.report_date_time',['invoice_type'=>'Customer Ledger'])
            <table>
                <tr>
                    <td style="width: 33%;" class="text-left small">Start Date: {{ $from }}</td>
                    <td style="width: 33%;" class="text-left small">End Date: {{ $to }}</td>
                    <td style="width: 33%;" class="text-right small">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div style="padding: 1.5rem;padding-bottom: 0">
            <table>
                <tr>
                    <td class="small">Customer Name: {{ @$customer->name }}</td>
                </tr>
                <tr>
                    <td class="small">Mobile: {{ @$customer->phone }}</td>
                </tr>
                {{-- <tr>
                    <td class="strong small">Money Receipt Number: {{ @$transaction->id }}</td>
                </tr> --}}
            </table>
        </div>
        @php
            $v_total_amount=0;
            $v_cash_paid_amount=0;
            $v_card_paid_amount=0;
            $v_online_paid_amount=0;
            $v_due_amount=0;
            $p_cash_paid_amount=0;
            $p_card_paid_amount=0;
            $p_online_paid_amount=0;
            $s_r_cash_paid_amount=0;
            $s_r_card_paid_amount=0;
            $s_r_online_paid_amount=0;
            $r_amount=0;
            $du_amount=0;
            $p_amount=0;
            $customer_due = 0;
            $advanceReceiptAmount = Helper::advanceReceiptAmount($store_id,$customer_id,$from,$to);
        @endphp

        <div style="padding: 1.5rem;">
            <h5>Voucher Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
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
                                $v_total_amount += $sale->grand_total;
				                // $v_cash_paid_amount += Helper::getPaymentTypeId($sale->id) == 1 ? $sale->paid_amount : 0;
				                // $v_card_paid_amount += Helper::getPaymentTypeId($sale->id) == 2 ? $sale->paid_amount : 0;
				                // $v_online_paid_amount += Helper::getPaymentTypeId($sale->id) == 5 ? $sale->paid_amount : 0;
                                $v_cash_paid_amount += $sale->payment_type_id == 1 ? $sale->paid_amount : 0;
				                $v_card_paid_amount += $sale->payment_type_id == 2 ? $sale->paid_amount : 0;
				                $v_online_paid_amount += $sale->payment_type_id == 5 ? $sale->paid_amount : 0;
				                $v_due_amount += $sale->due_amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$sale->id }}</td>
                                <td class="text-right">{{ $sale->grand_total }}</td>
                                {{-- <td class="text-right">{{ Helper::getCashPaidAmount($sale->id,$store_id,$from,$to) }}</td>
                                <td class="text-right">{{ Helper::getMobileBankingPaidAmount($sale->id,$store_id,$from,$to) }}</td>
                                <td class="text-right">{{ Helper::getOnlineBankPaidAmount($sale->id,$store_id,$from,$to) }}</td> --}}
                                <td class="text-right">{{ $sale->payment_type_id == 1 ? $sale->paid_amount : 0 }}</td>
                                <td class="text-right">{{ $sale->payment_type_id == 2 ? $sale->paid_amount : 0 }}</td>
                                <td class="text-right">{{ $sale->payment_type_id == 5 ? $sale->paid_amount : 0 }}</td>
                                <td class="text-right">{{ $sale->due_amount }}</td>
                                <td>{{ $sale->cpmments }}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="2">&nbsp;</td>
                            <td class="text-right">{{ $v_total_amount }}</td>
                            <td class="text-right">{{ $v_cash_paid_amount }}</td>
                            <td class="text-right">{{ $v_card_paid_amount }}</td>
                            <td class="text-right">{{ $v_online_paid_amount }}</td>
                            <td class="text-right">{{  $v_due_amount }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    @else
                        {{-- <tr><td colspan="8" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>

        @if(count($advanceReceiptAmount) > 0)
        <div style="padding: 1.5rem;">
            <h5>Advance Receipt</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Date</th>
                        <th>Payment Type</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="strong">
                        @foreach ($advanceReceiptAmount as $data)
                            @php
                                $advance_amount += $data->amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$data->date }}</td>
                                <td>{{ @$data->payment_type->name }}</td>
                                <td class="text-right">{{ $data->amount }}</td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div style="padding: 1.5rem;">
            <h5>Sale Return Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
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
                                //$r_amount += $saleReturn->refundable_amount;

                                $getCashPaidAmountSaleReturn = $saleReturn->payment_type_id == 1 ? $saleReturn->refund_amount : 0;
                                $getMobileBankingPaidAmountSaleReturn = $saleReturn->payment_type_id == 2 ? $saleReturn->refund_amount : 0;
                                $getOnlineBankPaidAmountSaleReturn = $saleReturn->payment_type_id == 5 ? $saleReturn->refund_amount : 0;
                                $s_r_cash_paid_amount += $getCashPaidAmountSaleReturn;
                                $s_r_card_paid_amount += $getMobileBankingPaidAmountSaleReturn;
                                $s_r_online_paid_amount += $getOnlineBankPaidAmountSaleReturn;
                                $customer_due = $saleReturn->customer_due;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$saleReturn->id }}</td>
                                <td class="text-right"> {{ $saleReturn->grand_total }}</td>
                                <td class="text-right"> {{ $saleReturn->customer_due }}</td>
                                <td class="text-right"> {{ $getCashPaidAmountSaleReturn }}</td>
                                <td class="text-right">{{ $getMobileBankingPaidAmountSaleReturn }}</td>
                                <td class="text-right">{{ $getOnlineBankPaidAmountSaleReturn }}</td>
                                <th>{{ $saleReturn->comments }}</th>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="6" class="text-center">No Result found</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- <div style="padding: 1.5rem;">
            <h5>Dues List</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-right">Grand Total</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($dues->isNotEmpty())
                        @foreach ($dues as $due)
                            @php
                                $du_amount += $due->amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$due->customer->name }}</td>
                                <td>{{ @$due->customer->phone }}</td>
                                <td class="text-right"> {{ @$due->amount }}</td>
                                <td>{{ $due->comments }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No Result found</td></tr>
                    @endif
                </tbody>
            </table>
        </div> --}}

        <div style="padding: 1.5rem;">
            <h5>Payment List</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
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
                                $p_amount += $payment->amount;
                                $getCashPaidAmount = $payment->payment_type_id == 1 ? $payment->amount : 0;
                                $getMobileBankingPaidAmount = $payment->payment_type_id == 2 ? $payment->amount : 0;
                                $getOnlineBankPaidAmount = $payment->payment_type_id == 5 ? $payment->amount : 0;
                                $p_cash_paid_amount += $getCashPaidAmount;
                                $p_card_paid_amount += $getMobileBankingPaidAmount;
                                $p_online_paid_amount += $getOnlineBankPaidAmount;

                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$payment->order_id }}</td>
                                <td class="text-right">{{ $getCashPaidAmount }}</td>
                                <td class="text-right">{{ $getMobileBankingPaidAmount }}</td>
                                <td class="text-right">{{ $getOnlineBankPaidAmount }}</td>
                                <td>{{ $payment->comments }}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="2">&nbsp;</td>
                            <td class="text-right">{{ $p_cash_paid_amount }}</td>
                            <td class="text-right">{{ $p_card_paid_amount }}</td>
                            <td class="text-right">{{ $p_online_paid_amount }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    @else
                        <tr><td colspan="8" class="text-center">No Result found</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @php
    $total_amount = ($v_total_amount+$du_amount)-$r_amount;
    $total_cash_paid_amount = ($v_cash_paid_amount+$p_cash_paid_amount) - $s_r_cash_paid_amount;
    $toal_card_paid_amount = ($v_card_paid_amount+$p_card_paid_amount) - $s_r_card_paid_amount;
    $toal_online_paid_amount = ($v_online_paid_amount+$p_online_paid_amount) - $s_r_online_paid_amount;
    // $due_amount = ($total_amount-$total_cash_paid_amount);
    $store_id = 1;
    $due_amount = Helper::customerDueAmount($store_id,$customer_id);
    $previousDueBalance = Helper::previousDueBalance($store_id,$customer_id,$from);
    @endphp

    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                <tr>
                    <th class="text-left strong">Balance B/F ({{ date('Y-m-d', strtotime($from. ' - 1 days')) }})</th>
                    <td class="currency">- {{ number_format($previousDueBalance,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Cash Paid Amount</th>
                    <td class="currency">{{ number_format($total_cash_paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Card Paid Amount</th>
                    <td class="currency">{{ number_format($toal_card_paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Online Paid Amount</th>
                    <td class="currency">{{ number_format($toal_online_paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Due Amount</th>
                    <td class="currency">{{ number_format($due_amount - $customer_due, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    </div>
</body>

</html>
