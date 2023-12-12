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
            font-size: .85rem;
        }

        .currency {}
    </style>
</head>

<body>
    <div>
        <div style="padding: 1.5rem;">
            @include('backend.common.reports.header')
            @include('backend.common.reports.date_time',['invoice_type'=>'Customer Ledger Report'])
            @include('backend.common.reports.customer')
            <table>
                <tr>
                    <td style="width: 33%;" class="text-left small">Start Date: {{ $from }}</td>
                    <td style="width: 33%;" class="text-left small">End Date: {{ $to }}</td>
                    <td style="width: 33%;" class="text-right small">&nbsp;</td>
                </tr>
            </table>
        </div>
        @if ($customerReports->isNotEmpty())
            <div style="padding: 1.5rem;">
                <table class="padding text-left small border-bottom">
                    <thead>
                        <tr class="strong" style="background: #eceff4;">
                            <th>SL NO</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            {{-- <th>Store</th> --}}
                            <th>Voucher TYpe</th>
                            <th>Payment Mode</th>
                            <th class="text-right">Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                        @foreach ($customerReports as $cus)
                            <tr class="">
                                <td>{{ $loop->index + 01 }}</td>
                                <td>{{ @$cus->date }}</td>
                                <td>{{ @$cus->id }}</td>
                                {{-- <td>{{ @$cus->store->name }}</td> --}}
                                <td>{{ @$cus->order_type->name }}</td>
                                <td>{{ @$cus->payment_type->name }}</td>
                                <td class="text-right">{{ number_format(@$cus->amount,2) }}</td>
                                <td>{{ $cus->order_type_id == 1 ? 'Paid' : 'Due' }}</td>
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
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($customerReports->sum('amount'),2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Paid Amount</th>
                    <td class="currency">
                        {{ number_format($customerReports->sum('amount') - Helper::ledgerCurrentBalance($customerReports),2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Due Amount</th>
                    <td class="currency">{{ number_format(Helper::ledgerCurrentBalance($customerReports),2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Previous Due Amount</th>
                    <td class="currency">{{ number_format($preBalance,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Final Due Amount</th>
                    <td class="currency">{{ number_format($preBalance + Helper::ledgerCurrentBalance($customerReports),2) }}</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;margin-right:auto;padding-top: 5px;" class="text-right sm-padding small strong pt-2">
            <tbody>
                <tr>
                    <th class="text-right strong">In Word :{{ucwords($digit->format($customerReports->sum('amount'),2))}} Only</th>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>

</html>
