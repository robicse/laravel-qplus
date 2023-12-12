@php
    use Salla\ZATCA\GenerateQrCode;
    use Salla\ZATCA\Tags\InvoiceDate;
    use Salla\ZATCA\Tags\InvoiceTaxAmount;
    use Salla\ZATCA\Tags\InvoiceTotalAmount;
    use Salla\ZATCA\Tags\Seller;
    use Salla\ZATCA\Tags\TaxNumber;

    $totalVat = @$sale->total_vat;
    $totalAmount = @$sale->grand_total;
    // $displayQRCodeAsBase64 = GenerateQrCode::fromArray([new Seller(getSalesmanNameById(@$sale->salesman_user_id)), new TaxNumber(@SalePrintSetting()->vat_number), new InvoiceDate($sale->date), new InvoiceTotalAmount($totalAmount), new InvoiceTaxAmount($totalVat)])->render();

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
            @include('backend.common.reports.date_time',['invoice_type'=>'Money Receipt Invoice','invoice_no'=>@$transaction->id])
        </div>
        <div style="padding: 1.5rem;padding-bottom: 0">
            <table>
                <tr>
                    <td class="strong smallstrong">Customer Name: {{ @$transaction->customer->name }}
                        </td>
                </tr>
                <tr>
                    <td class="strong small">Mobile: {{ @$transaction->customer->phone }}</td>
                </tr>
                {{-- <tr>
                    <td class="strong small">Money Receipt Number: {{ @$transaction->id }}</td>
                </tr> --}}
            </table>
        </div>
        <div style="padding: 1.5rem;">
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th width="10%">SL</th>
                        <th width="50%"> Payment Type</th>
                        <th width="20%" class="text-right"> Amount </th>
                        <th width="20%"> Remarks</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @if ($transaction)
                        <tr class="">
                            <td>1</td>
                            <td>
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
                            </td>
                            <td class="text-right currency">{{number_format($transaction->amount,2)}}</td>
                            <td>{{ @$transaction->comments }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div style="padding:0 1.5rem;">
            <table style="width: 40%;margin-right:auto;" class="text-left sm-padding small strong">
                <tbody>
                    <tr>
                        <th class="text-left strong">Total Total: {{ number_format(@$transaction->amount, 2) }} /- taka</td>
                    </tr>
                </tbody>
            </table>
            <table style="width: 100%;margin-right:auto;padding-top: 5px;" class="sm-padding small strong pt-2">
                <tbody>
                    <tr>
                        <th class="text-left strong" style="text-align: left">In Word :{{ucwords($digit->format(@$transaction->amount,2))}} taka Only</th>
                    </tr>

                </tbody>
            </table>
            <table style="width: 100%;margin-right:auto;padding-top: 50px;" class="sm-padding small strong pt-2">
                <tbody>
                    <tr>
                        <th class="text-left strong" style="text-align: left"><span style="border-top: 1px solid #ddd;">Authorized Signature</span></th>
                    </tr>
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
