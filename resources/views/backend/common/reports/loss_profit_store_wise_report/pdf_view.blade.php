<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Loss Profit Report </title>
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
            @include('backend.common.reports.report_date_time',['invoice_type'=>'Loss Profit Report'])
            <table>
                <tr>
                    <td style="width: 33%;" class="text-left small">Start Date: {{ $from }}</td>
                    <td style="width: 33%;" class="text-left small">End Date: {{ $to }}</td>
                    <td style="width: 33%;" class="text-right small">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div style="padding: 1.5rem;">
            @if ($storeWiseLossProfitReports->isNotEmpty())
                <table class="padding text-left small border-bottom">
                    <thead>
                        <tr class="strong" style="background: #eceff4;">
                            <th>Type</th>
                            <th>Invoice No</th>
                            <th>Store</th>
                            <th>Date</th>
                            <th class="text-right">Loss/Profit</th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                        @foreach ($storeWiseLossProfitReports as $sale)
                            <tr class="">
                                <td>Sale</td>
                                <td>{{ @$sale->id }}</td>
                                <td>{{ @$sale->store->name }}</td>
                                <td>{{ @$sale->voucher_date }}</td>
                                <td class="text-right">{{ $sale->profit_amount }}</td>
                            </tr>
                        @endforeach
                        @foreach ($storeWiseLossProfitReportsReturn as $saleReturn)
                            <tr>
                                <td>Sale Return</td>
                                <td>{{ $saleReturn->id }}</td>
                                <td>{{ $saleReturn->store->name }}</td>
                                <td>{{ $saleReturn->return_date }}</td>
                                <td class="text-right">{{ $saleReturn->profit_minus_amount }}</td>
                                {{-- <td>
                                    <a class="btn btn-warning btn-sm waves-effect" type="button"
                                        target="_blank"
                                        href="{{ route(\Request::segment(1) . '.sales.show', $sale->id) }}"><i
                                            class="fa fa-eye"></i></a>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h6 class="text-center">No Result found</h6>
            @endif
        </div>
    </div>

    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>

                <tr>
                    <th class="text-left strong">Total</th>
                    <td class="currency">{{ number_format($storeWiseLossProfitReports->sum('profit_amount') - $storeWiseLossProfitReportsReturn->sum('profit_minus_amount'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    </div>
</body>

</html>
