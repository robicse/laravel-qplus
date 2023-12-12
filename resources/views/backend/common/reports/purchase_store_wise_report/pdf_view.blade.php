<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Purchases Report </title>
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
            font-size: .85rem;
        }

        .currency {}
    </style>
</head>
<body>
    <div>
        <div style="padding: 1.5rem;">
            @include('backend.common.reports.header')
            @include('backend.common.reports.report_date_time',['invoice_type'=>'Purchase Report'])
            <table>
                <tr>
                    <td style="width: 33%;" class="text-left small">Start Date: {{ $from }}</td>
                    <td style="width: 33%;" class="text-left small">End Date: {{ $to }}</td>
                    <td style="width: 33%;" class="text-right small">&nbsp;</td>
                </tr>
            </table>
        </div>
        @if ($storeWisePurchaseReports->isNotEmpty())
            <div style="padding: 1.5rem;">
                <table class="padding text-left small border-bottom">
                    <thead>
                        <tr class="strong" style="background: #eceff4;">
                            <th>SL</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Invoice Number</th>
                            <th>Qty</th>
                            <th>Vat</th>
                            <th>Dis</th>
                            <th>Sub Total</th>
                            <th class="text-right">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                        @foreach ($storeWisePurchaseReports as $purchase)
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$purchase->purchase_date }}</td>
                                <td>{{ @$purchase->supplier->name }}</td>
                                <td>{{ @$purchase->id }}</td>
                                <td class="text-right"> {{ @$purchase->total_qty }}</td>
                                <td class="text-right"> {{ @$purchase->total_vat }}</td>
                                <td class="text-right"> {{ @$purchase->discount ? $purchase->discount : 0 }}</td>
                                <td class="text-right"> {{ @$purchase->sub_total }}</td>
                                <td class="text-right">{{ $purchase->grand_total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div>
                    <h2 class="text-center">No Result found</h2>
                </div>
        @endif
    </div>

    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                <tr>
                    <th class="text-left strong">Total Qty</th>
                    <td class="currency">{{ number_format($storeWisePurchaseReports->sum('total_qty'), 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Purchase Amount</th>
                    <td class="currency">{{ number_format($storeWisePurchaseReports->sum('grand_total'), 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Paid Amount</th>
                    <td class="currency">{{ number_format($storeWisePurchaseReports->sum('paid_amount'), 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Due Amount</th>
                    <td class="currency">{{ number_format($storeWisePurchaseReports->sum('due_amount'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    </div>
</body>

</html>
