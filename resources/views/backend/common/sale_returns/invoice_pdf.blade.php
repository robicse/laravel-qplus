@php
    use Salla\ZATCA\GenerateQrCode;
    use Salla\ZATCA\Tags\InvoiceDate;
    use Salla\ZATCA\Tags\InvoiceTaxAmount;
    use Salla\ZATCA\Tags\InvoiceTotalAmount;
    use Salla\ZATCA\Tags\Seller;
    use Salla\ZATCA\Tags\TaxNumber;

    $totalVat = @$saleReturn->total_vat;
    $totalAmount = @$saleReturn->grand_total;
    // $displayQRCodeAsBase64 = GenerateQrCode::fromArray([new Seller(getSalesmanNameById(@$saleReturn->salesman_user_id)), new TaxNumber(@SalePrintSetting()->vat_number), new InvoiceDate($saleReturn->date), new InvoiceTotalAmount($totalAmount), new InvoiceTaxAmount($totalVat)])->render();

@endphp
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice</title>
    <style media="all">
        @font-face {
            font-family: 'zahidularabic';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url('{{ storage_path('fonts/Adobe_Arabic_Regular.ttf') }}');


        }

        @font-face {
            font-family: 'bangla';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            /* src: url('{{ storage_path('fonts/Potro_Sans_Bangla_Bold.ttf') }}'); */
            src: '{{ storage_path('fonts/Potro_Sans_Bangla_Bold.ttf') }}';


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
            @include('backend.common.reports.date_time',['invoice_type'=>'Sale Invoice','invoice_no'=>@$saleReturn->id])
        </div>
        <div style="padding: 1.5rem;padding-bottom: 0">
            <table>
                <tr>
                    <td class="small">Customer: {{ @$saleReturn->customer->name }}
                        </td>
                </tr>
                <tr>
                    <td class="small">Address: {{ @$saleReturn->customer->address ?: $saleReturn->customer->email }},
                        {{ @$saleReturn->customer->phone }}</td>
                </tr>
                <tr>
                    <td class="small">Customer Phone: {{ @$saleReturn->customer->phone }}</td>
                </tr>
            </table>
        </div>

        <div style="padding: 1.5rem;">
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th width="35%">Name</th>
                        <th width="10%"> U/M</th>
                        <th width="15%"> Qty </th>
                        {{-- <th width="10%"> UN P</th> --}}
                        <th width="15%" class="text-right"> Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($saleReturnProducts as $key => $data)
                        <tr class="">
                            <td>{{ Str::limit(@$data->product->name, 50, '..') }} </td>
                            <td>
                                {{ @$data->product->unit->name }}
                            </td>
                            <td>{{ @$data->qty }}</td>
                            {{-- <td class="currency">{{ number_format(@$data->sale_price, 2) }}</td> --}}
                            <td class="text-right currency">
                                {{ number_format(@$data->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:0 1.5rem;">
            <table style="width: 40%;margin-right:auto;" class="text-left sm-padding small">
                <tbody>
                    <tr>
                        <th class="text-left">Grand Total:</th>
                        <td class="currency">{{ number_format(@$saleReturn->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Invoice Due:</th>
                        <td class="currency">{{ number_format(@$saleReturn->invoice_due, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Refundable Amount:</th>
                        <td class="currency">{{ number_format(@$saleReturn->refundable_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Refund Total:</th>
                        <td class="currency">{{ number_format(@$saleReturn->refund_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left">Due Total:</th>
                        <td class="currency">{{ number_format(@$saleReturn->due_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <table style="width: 100%;margin-left:auto;padding-top: 5px;" class="sm-padding small pt-2">
                <tbody>
                    <tr>
                        <th class="text-left">In Word :{{ucwords($digit->format($saleReturn->refundable_amount,2))}} Only</th>
                    </tr>
                    @if(count($transactions) > 0)
                        <tr>
                            <th class="text-left">
                                <ul>
                                    @foreach($transactions as $transaction)
                                        <li>
                                            Payment Type: {{@$transaction->payment_type->name}}<br/>
                                            @if(@$transaction->payment_type->name == 'Cheque')
                                                Cheque Number: {{$transaction->cheque_number}}<br/>
                                                bank Name: {{$transaction->bank_name}}<br/>
                                                Cheque Date: {{$transaction->cheque_date}}<br/>
                                            @elseif(@$transaction->payment_type->name == 'Condition')
                                                Note: {{$transaction->note}}<br/>
                                            @elseif(@$transaction->payment_type->name == 'Card' || @$transaction->payment_type->name == 'Online')
                                                Note: {{$transaction->transaction_number}}<br/>
                                            @endif
                                            Tk:{{number_format($transaction->amount,2)}} ({{$transaction->created_at}})
                                        </li>
                                    @endforeach
                                </ul>
                            </th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- <div style="padding:0 1.5rem;">
            <div class="col-md-12" style="text-align:right;float:right;">
                <span>Print Date: {{date('Y-m-d H:i:s')}} Computer Generated Invoice</span>
            </div>
        </div> --}}

    </div>
</body>

</html>
