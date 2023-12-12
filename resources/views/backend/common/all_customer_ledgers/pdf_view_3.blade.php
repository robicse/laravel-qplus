<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Customer Ledger Report </title>
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
            @include('backend.common.reports.report_date_time',['invoice_type'=>'All Customer Ledger'])
        </div>
        @php
            $total_amount = 0;
            $total_paid_amount = 0;
            $total_due_amount = 0;
            $total_opening_balance = 0;
            $allCustomerOpeningBalances = Helper::allCustomerOpeningBalance($store_id);
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
            <h5>All Customer Ledgers</h5>
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th>Customer Name</th>
                        <th>Customer Phone</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-right">Total Paid Amount</th>
                        <th class="text-right">Total Due Amount</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($sales->isNotEmpty())
                        @foreach ($sales as $sale)
                            @php
                                // dd($sale);
                                $allCustomerLedger = Helper::allCustomerLedger($sale->store_id,$sale->customer_id);

                                $total_amount += $allCustomerLedger['total_amount'];
                                $total_paid_amount += $allCustomerLedger['total_paid_amount'];
                                $total_due_amount += $allCustomerLedger['total_due_amount'] ;
                            @endphp
                            <tr class="">
                                <td>{{ @$sale->customer->name }}</td>
                                <td>{{ @$sale->customer->phone }}</td>
                                <td class="text-right">{{ $allCustomerLedger['total_amount'] }}</td>
                                <td class="text-right">{{ $allCustomerLedger['total_paid_amount'] }}</td>
                                <td class="text-right">{{ $allCustomerLedger['total_due_amount'] }}</td>
                            </tr>
                        @endforeach

                        <tr class="">
                            <td colspan="2">&nbsp;</td>
                            <td class="text-right">{{ $total_amount }}</td>
                            <td class="text-right">{{ $total_paid_amount }}</td>
                            <td class="text-right">{{ $total_due_amount }}</td>
                        </tr>

                    @endif
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>
