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
                <tr>
                    <td class="small">Address: {{ @$customer->address }}</td>
                </tr>
            </table>
        </div>
        @php
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

            //$customer_due = 0;
            $advanceReceiptAmount = Helper::advanceReceiptAmount($store_id,$customer_id,$from,$to);
            $opening_balance = 0;
            $customerOpeningBalance = Helper::customerOpeningBalance($store_id,$customer_id,$from,$to);
            if($customerOpeningBalance){
                $opening_balance =$customerOpeningBalance->opening_balance;
            }
        @endphp

        @if($customerOpeningBalance && ($customerOpeningBalance->opening_balance > 0))
            <div style="padding: 1.5rem;">
                <h5>Opening Balance</h5>
                <table class="padding text-left small border-bottom">
                    <thead>
                        <tr class="strong" style="background: #eceff4;">
                            <th>Start Date</th>
                            <th>Due Amount</th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                        <tr style="text-align: center">
                            <td>{{ $customerOpeningBalance->start_date }}</td>
                            <td>{{ $customerOpeningBalance->opening_balance }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div style="padding: 1.5rem;">
            <h5>Voucher Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Date</th>
                        <th>Voucher NO</th>
                        <th>Voucher Type</th>
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

                        @foreach ($sales as $key => $sale)
                            @php
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
                                // $advance_minus_amount +=$sale->advance_minus_amount;

                            @endphp
                            <tr class="">
                                <td>{{ $key + 1 }}</td>
                                <td>{{ @$sale->date }}</td>
                                <td>{{ @$sale->order_id }}</td>
                                <td>{{ @$sale->order_type }}</td>
                                <td class="text-right">
                                    @if(@$sale->order_type_id == 2 && @$sale->order_type == 'Sale')
                                        {{ $sale->total }}
                                    @elseif(@$sale->order_type == 'Previous Due' || @$sale->order_type == 'Received')
                                        {{ $sale->amount }}
                                    @else
                                        {{ $sale->total }}
                                    @endif
                                    {{-- {{ @$sale->order_type == 'Previous Due' || 'Received' ? $sale->amount : $sale->total}} --}}
                                </td>
                                <td class="text-right">{{ $getCashVoucherAmount }}</td>
                                <td class="text-right">{{ $getMobileBankingVoucherAmount }}</td>
                                <td class="text-right">{{ $getOnlineBankVoucherAmount }}</td>
                                {{-- <td class="text-right">{{ $sale->due }}</td> --}}
                                <td class="text-right">
                                    @if(@$sale->order_type == 'Sale')
                                    {{  $sale->due }}
                                    @endif
                                </td>
                                <td>{{ $sale->comments }}</td>
                            </tr>
                        @endforeach
                        @php
                            $total_cash_paid_amount = $v_total_cash_paid_amount + $p_total_cash_paid_amount;
                            $total_card_paid_amount = $v_total_card_paid_amount + $p_total_card_paid_amount;
                            $total_online_paid_amount = $v_total_online_paid_amount + $p_total_online_paid_amount;
                            $total_due_amount = $v_total_amount - ($total_cash_paid_amount + $total_card_paid_amount + $total_online_paid_amount);
                        @endphp
                        {{-- <tr class="">
                            <td colspan="4">&nbsp;</td>
                            <td class="text-right">{{ $v_total_amount }}</td>
                            <td class="text-right">{{ $total_cash_paid_amount }}</td>
                            <td class="text-right">{{ $total_card_paid_amount }}</td>
                            <td class="text-right">{{ $total_online_paid_amount }}</td>
                            <td class="text-right">{{ $total_due_amount }}</td>
                            <td>&nbsp;</td>
                        </tr> --}}

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
                            <th>Type</th>
                            <th>Payment Type</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                            @foreach ($advanceReceiptAmount as $data)
                                @php
                                    if(@$data->type == 'Advance Minus'){
                                        $advance_receipt_amount += $data->amount;
                                    }
                                @endphp
                                <tr class="">
                                    <td>{{ $loop->index + 01 }}</td>
                                    <td>{{ @$data->date }}</td>
                                    <td>{{ @$data->type }}</td>
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
                                $s_r_total_refundable_amount += $saleReturn->refundable_amount;
                                $getCashPaidAmountSaleReturn = $saleReturn->payment_type_id == 1 ? $saleReturn->refund_amount : 0;
                                $getMobileBankingPaidAmountSaleReturn = $saleReturn->payment_type_id == 2 ? $saleReturn->refund_amount : 0;
                                $getOnlineBankPaidAmountSaleReturn = $saleReturn->payment_type_id == 5 ? $saleReturn->refund_amount : 0;
                                $s_r_total_cash_paid_amount += $getCashPaidAmountSaleReturn;
                                $s_r_total_card_paid_amount += $getMobileBankingPaidAmountSaleReturn;
                                $s_r_total_online_paid_amount += $getOnlineBankPaidAmountSaleReturn;
                                //$customer_due = $saleReturn->customer_due;
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
                        {{-- <tr><td colspan="6" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @php
        $grand_total_cash_paid_amount = $total_cash_paid_amount - $s_r_total_cash_paid_amount;
        $grand_toal_card_paid_amount = $total_card_paid_amount - $s_r_total_card_paid_amount;
        $grand_toal_online_paid_amount = $total_online_paid_amount - $s_r_total_online_paid_amount;
        $grand_toal_paid_amount = $grand_total_cash_paid_amount + $grand_toal_card_paid_amount + $grand_toal_online_paid_amount;
        $grand_toal_refund_amount = $s_r_total_cash_paid_amount + $s_r_total_card_paid_amount + $s_r_total_online_paid_amount;

        $previousDueBalance = Helper::previousDueBalance($store_id,$customer_id,$from);

        $due_amount = ($v_total_amount - $grand_toal_paid_amount) - $grand_toal_refund_amount;
    @endphp

    <div style="padding: 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                <tr>
                    <th class="text-left strong">Balance B/F ({{ date('Y-m-d', strtotime($from. ' - 1 days')) }})</th>
                    <td class="currency"> {{ number_format($previousDueBalance,2) }}</td>
                </tr>
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
                    {{-- <td class="currency">{{ number_format($due_amount + $opening_balance, 2) }}</td> --}}
                    <td class="currency">{{ number_format((($current_date_range_previous_due_amount+$due_amount + $previousDueBalance) - ($s_r_total_refundable_amount + $advance_receipt_amount)), 2) }}</td>
                    {{-- <td class="currency">{{ number_format($v_total_due_amount  + $opening_balance, 2) }}</td> --}}
                </tr>
            </tbody>
        </table>
    </div>

    </div>
</body>

</html>
