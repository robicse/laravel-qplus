<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Today Voucher List </title>
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
            @include('backend.common.reports.report_date_time',['invoice_type'=>'Today Voucher Report'])
        </div>
        <div style="padding: 1.5rem;">
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>SL</th>
                        <th>Date</th>
                        <th>Voucher NO</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Qty</th>
                        {{-- <th class="text-right">Sub Total</th>
                        <th class="text-right">Discount</th> --}}
                        <th class="text-right">Grand Total</th>
                        <th class="text-right">Paid Amount</th>
                        {{-- <th class="text-right">Comment</th> --}}
                    </tr>
                </thead>
                @if ($todayVoucherReports->isNotEmpty())
                    <tbody class="strong">
                        @foreach ($todayVoucherReports as $data)
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$data->voucher_date }}</td>
                                <td>{{ @$data->hc_voucher_number }}</td>
                                <td>{{ @$data->customer->name }}</td>
                                <td>{{ @$data->customer->phone }}</td>
                                <td>{{ @$data->total_quantity }}</td>>
                                {{-- <td class="text-right"> {{ @$data->sub_total }}</td>
                                <td class="text-right"> {{ @$data->discount_amount }}</td> --}}
                                <td class="text-right">{{ $data->grand_total }}</td>
                                <td class="text-right"> {{ @$data->paid_amount }}</td>
                                {{-- <td class="text-right">{{ $data->comments }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <tbody class="strong">
                        <tr><td colspan="8" class="text-center">No Result found</td></tr>
                    </tbody>
                @endif
            </table>
        </div>
    </div>
</body>

</html>
