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
    <div style="padding: 1.5rem;">
        @include('backend.common.reports.header')
        @include('backend.common.reports.report_date_time',['invoice_type'=>'Balance Sheet Report'])
        {{-- @include('backend.common.reports.customer') --}}
        <table>
            <tr>
                <td style="width: 33%;" class="text-left small">Start Date: {{ $from }}</td>
                <td style="width: 33%;" class="text-left small">End Date: {{ $to }}</td>
                <td style="width: 33%;" class="text-right small">&nbsp;</td>
            </tr>
        </table>
    </div>

    <div style="padding: 1.5rem;">
        <table class="padding text-left small border-bottom">
            <thead>
                <tr class="strong" style="background: #eceff4;">
                    <th>SL NO</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Receipt Invoice No</th>
                    {{-- <th>Voucher TYpe</th> --}}
                    <th>Payment Mode</th>
                    <th class="text-right">Paid Amount</th>
                    <th class="text-right">Due Amount</th>
                    <th>Comments</th>
                </tr>
            </thead>
            @if ($customerBalanceSheetReports->isNotEmpty())
            <tbody class="strong">
                @foreach ($customerBalanceSheetReports as $data)
                    @php
                        if(@$data->order_type_id == 1){
                            $paid_amount = @$data->amount;
                            $due_amount = 0;
                        }else{
                            $paid_amount = 0;
                            $due_amount = @$data->amount;
                        }
                    @endphp
                    <tr>
                        <td>{{ $loop->index + 01 }}</td>
                        <td>{{ @$data->date }}</td>
                        <td>{{ @$data->customer->name }}</td>
                        <td>{{ @$data->customer->phone }}</td>
                        <td>{{ @$data->order_type }}</td>
                        <td>{{ @$data->id }}</td>
                        <td>
                            @if(@$data->payment_type_id)
                            {{ Helper::getPaymentTypeName(@$data->payment_type_id) }}
                            @endif
                        </td>
                        <td class="text-right">{{ number_format(@$paid_amount,2) }}</td>
                        <td class="text-right">{{ number_format(@$due_amount,2) }}</td>
                        <td>{{ @$data->comments }}</td>
                    </tr>
                @endforeach
            </tbody>
            @endif
        </table>
    </div>
    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                <tr>
                    <th class="text-left strong">Balance B/F ({{ date('Y-m-d', strtotime($from. ' - 1 days')) }})</th>
                    <td class="currency">- {{ number_format($customerOpeningDueBalance,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($customerTotalAmount,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Paid Amount</th>
                    <td class="currency">
                        {{ number_format($customerPaidAmount,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Due Amount</th>
                    <td class="currency">{{ number_format($customerDueAmount,2) }}</td>
                </tr>
                @if(@$customerReturnPaidAmount > 0)
                <tr>
                    <th class="text-left strong">Refund Amount</th>
                    <td class="currency">
                        {{ number_format($customerReturnPaidAmount,2) }}</td>
                </tr>
                @endif
                <tr>
                    <th class="text-left strong">Balance C/F ({{ date('Y-m-d', strtotime($to. ' 1 days')) }})</th>
                    <td class="currency">- {{ number_format($customerDueAmount + @$customerOpeningDueBalance,2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>




    {{-- <div style="padding: 1.5rem;">
        <table class="padding text-left small border-bottom">
            <thead>
                <tr class="strong" style="background: #eceff4;">
                    <th>SL NO</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Receipt Invoice No</th>
                    <th class="text-right">Paid Amount</th>
                    <th class="text-right">Due Amount</th>
                    <th>Comments</th>
                </tr>
            </thead>
            @if ($supplierBalanceSheetReports->isNotEmpty())
            <tbody class="strong">
                @foreach ($supplierBalanceSheetReports as $data)
                    @php
                        if(@$data->order_type_id == 1){
                            $paid_amount = @$data->amount;
                            $due_amount = 0;
                        }else{
                            $paid_amount = 0;
                            $due_amount = @$data->amount;
                        }
                    @endphp
                    <tr>
                        <td>{{ $loop->index + 01 }}</td>
                        <td>{{ @$data->date }}</td>
                        <td>{{ @$data->supplier->name }}</td>
                        <td>{{ @$data->supplier->phone }}</td>
                        <td>{{ @$data->order_type }}</td>
                        <td>{{ @$data->id }}</td>
                        <td class="text-right">{{ number_format(@$paid_amount,2) }}</td>
                        <td class="text-right">{{ number_format(@$due_amount,2) }}</td>
                        <td>{{ @$data->comments }}</td>
                    </tr>
                @endforeach
            </tbody>
            @endif
        </table>
    </div>
    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
                <tr>
                    <th class="text-left strong">Balance B/F ({{ date('Y-m-d', strtotime($from. ' - 1 days')) }})</th>
                    <td class="currency">- {{ number_format($supplierOpeningDueBalance,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Total Amount</th>
                    <td class="currency">{{ number_format($supplierTotalAmount,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Paid Amount</th>
                    <td class="currency">
                        {{ number_format($supplierPaidAmount,2) }}</td>
                </tr>
                <tr>
                    <th class="text-left strong">Due Amount</th>
                    <td class="currency">{{ number_format($supplierDueAmount,2) }}</td>
                </tr>
                @if(@$supplierReturnPaidAmount > 0)
                <tr>
                    <th class="text-left strong">Refund Amount</th>
                    <td class="currency">
                        {{ number_format($supplierReturnPaidAmount,2) }}</td>
                </tr>
                @endif
                <tr>
                    <th class="text-left strong">Balance C/F ({{ date('Y-m-d', strtotime($to. ' 1 days')) }})</th>
                    <td class="currency">- {{ number_format($supplierDueAmount + @$supplierOpeningDueBalance,2) }}</td>
                </tr>
            </tbody>
        </table>
    </div> --}}

</body>

</html>
