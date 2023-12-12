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
        @endphp

        <div style="padding: 1.5rem;">
            <h5>Voucher Lists</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Voucher NO</th>
                        {{-- <th>Date</th> --}}
                        <th>Customer</th>
                        <th>Contact NO</th>
                        {{-- <th>Qty</th>
                        <th>Vat</th>
                        <th>Dis</th>
                        <th class="text-right">Sub Total</th> --}}
                        <th class="text-right">Total Amount</th>
                        <th class="text-right">Cash Paid Amount</th>
                        <th class="text-right">Mobile Banking Paid Amount</th>
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
				                $v_paid_amount += $sale->paid_amount;
                            @endphp
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$sale->id }}</td>
                                {{-- <td>{{ @$sale->voucher_date }}</td> --}}
                                <td>{{ @$sale->customer->name }}</td>
                                <td>{{ @$sale->customer->phone }}</td>
                                {{-- <td class="text-right"> {{ @$sale->total_quantity }}</td>
                                <td class="text-right"> {{ @$sale->total_vat }}</td>
                                <td class="text-right"> {{ @$sale->discount ? $sale->discount : 0 }}</td>
                                <td class="text-right"> {{ @$sale->sub_total }}</td> --}}
                                <td class="text-right">{{ $sale->grand_total }}</td>
                                <td class="text-right">{{ Helper::getCashPaidAmount($sale->id,$store_id,$from,$to) }}</td>
                                <td class="text-right">{{ Helper::getMobileBankingPaidAmount($sale->id,$store_id,$from,$to) }}</td>
                                <td class="text-right">{{ Helper::getOnlineBankPaidAmount($sale->id,$store_id,$from,$to) }}</td>
                                <td class="text-right">{{ $sale->due_amount }}</td>
                                <td>{{ $sale->cpmments }}</td>
                            </tr>
                        @endforeach
                    @else
                        {{-- <tr><td colspan="8" class="text-center">No Result found</td></tr> --}}
                    @endif
                </tbody>
            </table>
        </div>

        {{-- <div style="padding: 1.5rem;">
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

        <div style="padding: 1.5rem;">
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
    // $total_amount = ($v_total_amount+$du_amount)-$r_amount;
    // $paid_amount = ($v_paid_amount+$p_amount);
    // $due_amount = ($total_amount-$paid_amount);
    @endphp

    {{-- <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                <tr>
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Paid Amount</th>
                    <td class="currency">{{ number_format($paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Due Amount</th>
                    <td class="currency">{{ number_format($due_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div> --}}

    </div>
</body>

</html>
