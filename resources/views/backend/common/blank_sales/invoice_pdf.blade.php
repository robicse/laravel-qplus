@php
    use Salla\ZATCA\GenerateQrCode;
    use Salla\ZATCA\Tags\InvoiceDate;
    use Salla\ZATCA\Tags\InvoiceTaxAmount;
    use Salla\ZATCA\Tags\InvoiceTotalAmount;
    use Salla\ZATCA\Tags\Seller;
    use Salla\ZATCA\Tags\TaxNumber;

    $totalVat = @$blankSale->total_vat;
    $totalAmount = @$blankSale->grand_total;
    // $displayQRCodeAsBase64 = GenerateQrCode::fromArray([new Seller(getSalesmanNameById(@$blankSale->salesman_user_id)), new TaxNumber(@SalePrintSetting()->vat_number), new InvoiceDate($blankSale->date), new InvoiceTotalAmount($totalAmount), new InvoiceTaxAmount($totalVat)])->render();

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
            @include('backend.common.reports.date_time',['invoice_type'=>'Blank Sale Invoice','invoice_no'=>@$blankSale->id])
        </div>

        <div style="padding: 1.5rem;padding-bottom: 0">
            <table>
                <tr>
                    <td class="strong smallstrong">Customer: {{ @$blankSale->customer->name }}
                        </td>
                </tr>
                <tr>
                    <td class="strong small">Address: {{ @$blankSale->customer->address ?: $blankSale->customer->email }},
                        {{ @$blankSale->customer->phone }}</td>
                </tr>
                <tr>
                    <td class="strong small">Customer Phone: {{ @$blankSale->customer->phone }}</td>
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
                    </tr>
                </thead>
                <tbody class="strong">
                    @foreach ($blankSaleProducts as $key => $data)
                        <tr>
                            <td>{{ Str::limit(@$data->product->name, 50, '..') }} </td>
                            <td>
                                {{ @$data->product->unit->name }}
                            </td>
                            <td class="strong">{{ @$data->qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="barcode_details">
            <div style="width: 100%; margin:20px;">
                <div style="width: 50%; float:left; text-align:center;">
                    <table style="width: 100%;margin-right:auto;margin-top: 20px;">
                        <tbody>
                            <tr>
                                <th style="text-align: left">In Word :{{ucwords($digit->format($blankSale->grand_total,2))}} taka only</th>
                            </tr>
                            @if(@$blankSale->payment_type_id)
                                <tr>
                                    <th style="text-align: left">
                                        <ul>
                                            <li>
                                                Payment Type: {{@$blankSale->payment_type->name}}<br/>
                                                @if(@$blankSale->payment_type->name == 'Cheque')
                                                    Cheque Number: {{$blankSale->cheque_number}}<br/>
                                                    bank Name: {{$blankSale->bank_name}}<br/>
                                                    Cheque Date: {{$blankSale->cheque_date}}<br/>
                                                    Tk:{{number_format($blankSale->grand_total,2)}} ({{$blankSale->created_at}})
                                                @endif
                                            </li>
                                        </ul>
                                    </th>
                                </tr>
                            @endif
                            <tr><th>&nbsp;</th></tr>
                            <tr style="padding-top:20px;margin-top: 160px;">
                                <th style="text-align: left;">Comments: {{ @$blankSale->comments }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="width: 40%; float:right; text-align:right;">
                    <table style="width: 80%;margin-right:auto;">
                        <tbody>
                            <tr>
                                <th class="text-right ">Grand Total:</th>
                                <td class="currency">{{ number_format(@$blankSale->grand_total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br style="clear: left;" />
            </div>
        </div>

    </div>
</body>

</html>
