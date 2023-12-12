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
            $v_paid_amount=0;
            $r_amount=0;
            $du_amount=0;
            $p_amount=0;
            $advance_amount = 0;
            $advanceReceiptAmount = Helper::advanceReceiptAmount($store_id,null,$from,$to);
        @endphp

        <div style="padding: 1.5rem;">
            <h5>Voucher Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Date Time</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Contact NO</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-right">Cash Paid Amount</th>
                        <th class="text-right">Mobile Banking Paid Amount</th>
                        <th class="text-right">Online Bank Paid Amount</th>
                        <th class="text-right">Due Amount</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($payments->isNotEmpty())
                        @foreach ($payments as $payment)
                            @php
                                $grand_total = $payment->sale->grand_total;
                                $v_total_amount += $grand_total;

                                // $getCashPaidAmount = Helper::getCashPaidAmount($payment->order_id,$store_id,$payment->date);
                                // $getMobileBankingPaidAmount = Helper::getMobileBankingPaidAmount($payment->order_id,$store_id,$payment->date);
                                // $getOnlineBankPaidAmount = Helper::getOnlineBankPaidAmount($payment->order_id,$store_id,$payment->date);
                                $getCashPaidAmount = $payment->payment_type_id == 1 ? $payment->amount : 0;
                                $getMobileBankingPaidAmount = $payment->payment_type_id == 2 ? $payment->amount : 0;
                                $getOnlineBankPaidAmount = $payment->payment_type_id == 5 ? $payment->amount : 0;
                                $getUntilNowTotalPaidAmount = Helper::getUntilNowTotalPaidAmount($payment->order_id,$payment->created_at);
				                $v_paid_amount += $getCashPaidAmount + $getMobileBankingPaidAmount + $getOnlineBankPaidAmount;
                                $du_amount += $getUntilNowTotalPaidAmount;


                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$payment->created_at }}</td>
                                <td>{{ @$payment->order_id }}</td>
                                <td>{{ @$payment->customer->name }}</td>
                                <td>{{ @$payment->customer->phone }}</td>
                                <td class="text-right">{{ $grand_total }}</td>
                                <td class="text-right">{{ $getCashPaidAmount }}</td>
                                <td class="text-right">{{ $getMobileBankingPaidAmount }}</td>
                                <td class="text-right">{{ $getOnlineBankPaidAmount }}</td>
                                <td class="text-right">{{ $grand_total - $getUntilNowTotalPaidAmount }}</td>
                                <td>{{ $payment->comments }}</td>
                            </tr>
                        @endforeach
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
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Payment Type</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    {{-- @if ($saleReturns->isNotEmpty()) --}}
                        @foreach ($advanceReceiptAmount as $data)
                            @php
                                $advance_amount += $data->amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$data->date }}</td>
                                <td>{{ @$data->customer->name }}</td>
                                <td>{{ @$data->customer->phone }}</td>
                                <td>{{ @$data->payment_type->name }}</td>
                                <td class="text-right">{{ $data->amount }}</td>
                            </tr>
                        @endforeach
                    {{-- @else
                        <tr><td colspan="8" class="text-center">No Result found</td></tr>
                    @endif --}}
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
                        <th>Date</th>
                        <th>Voucher Return NO</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Qty</th>
                        <th class="text-right">Grand Total</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($saleReturns->isNotEmpty())
                        @foreach ($saleReturns as $saleReturn)
                            @php
                                $r_amount += $saleReturn->refundable_amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$saleReturn->return_date }}</td>
                                <td>{{ @$saleReturn->id }}</td>
                                <td>{{ @$saleReturn->sale_id }}</td>
                                <td>{{ @$saleReturn->customer->name }}</td>
                                <td>{{ @$saleReturn->customer->phone }}</td>
                                <td class="text-right"> {{ @$saleReturn->total_quantity }}</td>
                                <td class="text-right">{{ $saleReturn->refundable_amount }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No Result found</td></tr>
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
        </div>

        <div style="padding: 1.5rem;">
            <h5>Payment List</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Pay Date</th>
                        <th>Payment Type</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-right">Grand Total</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($payments->isNotEmpty())
                        @foreach ($payments as $payment)
                            @php
                                $p_amount += $payment->amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$payment->date }}</td>
                                <td>{{ @$payment->payment_type->name }}</td>
                                <td>{{ @$payment->customer->name }}</td>
                                <td>{{ @$payment->customer->phone }}</td>
                                <td class="text-right"> {{ @$payment->amount }}</td>
                                <td>{{ $payment->comments }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No Result found</td></tr>
                    @endif
                </tbody>
            </table>
        </div> --}}
    </div>

    @php
    $total_amount = ($v_total_amount+$du_amount)-$r_amount;
    $paid_amount = ($v_paid_amount+$p_amount+$advance_amount);
    // $due_amount = ($total_amount-$paid_amount);
    $totalInvoiceAmount = Helper::totalInvoiceAmount($store_id,null,$from,$to);
    $totalInvoiceDueAmount = Helper::totalInvoiceDueAmount($store_id,null,$from,$to);
    // dd($totalInvoiceAmount);
    @endphp

    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                {{-- <tr>
                    <th class="text-left strong">Balance B/F ({{ date('Y-m-d', strtotime($from. ' - 1 days')) }})</th>
                    <td class="currency">- {{ number_format($openingDueBalance,2) }}</td>
                </tr> --}}
                <tr>
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($totalInvoiceAmount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Paid Amount</th>
                    <td class="currency">{{ number_format($paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Due Amount</th>
                    {{-- <td class="currency">{{ number_format($du_amount, 2) }}</td> --}}
                    <td class="currency">{{ number_format($totalInvoiceDueAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    </div>
</body>

</html>
