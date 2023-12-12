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

        .footer_div {
            position:absolute;
            bottom: 10 !important;
            /*border-top: 1px solid #000000;*/
            width:100%;
            font-size: 10px !important;
            padding-bottom: 20px;
        }
    </style>
</head>

<body>
    <div>
        <div style="padding: 1.5rem;">
            @include('backend.common.reports.header')
            @include('backend.common.reports.date_time',['invoice_type'=>'Sales Invoice','invoice_no'=>@$sale->id,'date'=>@$sale->voucher_date,'created_at'=>@$sale->created_at])
        </div>

        <div style="padding: 1.5rem;padding-bottom: 0">
            <table>
                <tr>
                    <td class=" small">Customer: {{ @$sale->customer->name }}
                        </td>
                </tr>
                <tr>
                    <td class=" small">Address: {{ @$sale->customer->address ?: $sale->customer->email }}</td>
                </tr>
                <tr>
                    <td class=" small">Customer Phone: {{ @$sale->customer->phone }}</td>
                </tr>
            </table>
        </div>

        <div style="padding: 1.5rem;">
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="" style="background: #eceff4;">
                        <th width="35%">Name</th>
                        <th width="10%"> Unit</th>
                        <th width="15%"> Qty </th>
                        <th width="10%"> Unit Price</th>
                        <th width="15%" class="text-right"> Total Price</th>
                    </tr>
                </thead>
                <tbody class="text-align: center;">
                    @foreach ($saleProducts as $key => $sales_info)
                        <tr class="">
                            <td style="text-align: center">{{ Str::limit(@$sales_info->product->name, 50, '..') }} </td>
                            <td style="text-align: center">
                                {{ @$sales_info->product->unit->name }}
                            </td>
                            <td style="text-align: center">{{ @$sales_info->qty }}</td>
                            <td style="text-align: center">{{ number_format(@$sales_info->sale_price, 2) }}</td>
                            <td style="text-align: center">
                                {{ number_format(@$sales_info->qty * @$sales_info->sale_price, 2) }}</td>
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
                                <th style="text-align: left">In Word :{{ucwords($digit->format($sale->grand_total,2))}} taka only</th>
                            </tr>
                            @if(count($transactions) > 0)
                                <tr>
                                    <th style="text-align: left">
                                        <ul>
                                            @foreach($transactions as $transaction)
                                                <li>
                                                    Payment Type: {{@$transaction->payment_type->name}}<br/>
                                                    @if(@$transaction->payment_type->name == 'Cheque')
                                                        Cheque Number: {{$transaction->cheque_number}}<br/>
                                                        bank Name: {{$transaction->bank_name}}<br/>
                                                        Cheque Date: {{$transaction->cheque_date}}<br/>
                                                        Tk:{{number_format($transaction->amount,2)}} ({{$transaction->created_at}})
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </th>
                                </tr>
                            @endif
                            <tr><th>&nbsp;</th></tr>
                            <tr style="padding-top:20px;margin-top: 160px;">
                                <th style="text-align: left;">Comments: {{ @$sale->comments }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="width: 40%; float:right; text-align:right;">
                    <table style="width: 80%;margin-right:auto;">
                        <tbody>
                            <tr>
                                <th class="text-right">Sub Total:</th>
                                <td class="currency">{{ number_format(@$sale->sub_total, 2) }}</td>
                            </tr>
                            <tr>
                                <th class=" text-right">VAT:</th>
                                <td class="currency">{{ number_format(@$sale->total_vat, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th class=" text-right">Discount:</th>
                                <td class="currency">{{ number_format(@$sale->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="text-right ">Grand Total:</th>
                                <td class="currency">{{ number_format(@$sale->grand_total, 2) }}</td>
                            </tr>
                            <tr style="display: none">
                                <th class="text-right ">Advance Total:</th>
                                <td class="currency">{{ number_format(@$sale->advance_minus_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="text-right ">Paid Amount:</th>
                                <td class="currency">{{ number_format(@$sale->paid_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="text-right ">Due Amount:</th>
                                <td class="currency">{{ number_format(@$sale->due_amount, 2) }}</td>
                            </tr>
                            {{-- <tr>
                                <th class="text-right ">Previous Due:</th>
                                <td class="currency">{{ number_format(@$previousDue, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="text-right ">Final Due:</th>
                                <td class="currency">{{ number_format(@$sale->due_amount + @$previousDue, 2) }}</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
                <br style="clear: left;" />
            </div>
        </div>

        @if(count($saleReturnProducts) >0 )
            <div style="padding: 1.5rem 0 0 1.5rem">
                <h4>Sales Return</h4>
            </div>
            <div style="padding: 1.5rem;">
                <table class="padding text-left small border-bottom">
                    <thead>
                        <tr class="" style="background: #eceff4;">
                            <th width="35%">Name</th>
                            <th width="10%"> U/M</th>
                            <th width="15%"> Qty </th>
                            <th width="15%" class="text-right"> Refund Amount</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($saleReturnProducts as $key => $data)
                            <tr class="">
                                <td>{{ Str::limit(@$data->product->name, 50, '..') }} </td>
                                <td>
                                    {{ @$data->product->unit->name }}
                                </td>
                                <td class="">{{ @$data->qty }}</td>
                                <td class="text-right currency">
                                    {{ number_format(@$data->amount* @$data->qty, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:0 1.5rem;">
                <table style="width: 40%;margin-right:auto;" class="text-left sm-padding small ">
                    <tbody>
                        <tr>
                            <th class=" text-left">Refundable Amount</th>
                            <td class="currency">{{ number_format(@$refundable_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th class=" text-left">Refund Amount:</th>
                            <td class="currency">{{ number_format(@$refund_amount, 2) }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <th class=" text-left">Due Amount:</th>
                            <td class="currency">{{ number_format(@$due_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <table style="width: 100%;margin-right:auto;padding-top: 5px;" class="sm-padding small  pt-2">
                    <tbody>
                        <tr>
                            <th class="text-right " style="text-align: right">In Word :{{ucwords($digit->format(@$refundable_amount,2))}} taka only</th>
                        </tr>
                        <tr>
                            <th class="text-left " style="text-align: left">Comments:</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div class="row footer_div" style="margin-top: 50px;">
            <div class="row" style="margin-top: 50px;display: block;">
                <div class="col-md-6" style="text-align: left;float: left;width: 70%;margin-left: 10px; display: inline-block;">
                    <span style="border-bottom: solid 1px #000;text-align: center;width:400px;margin-top: -42px;font-size: 12px">Declaration</span><br>
                    <p>We declare that this voucher shows the actual price of the goods<br/> described and that all particulars are true and correct</p>
                </div>
                <div class="col-md-6" style="text-align:right;float: right;width: 30%;margin-right: 10px; display: inline-block;">
                    <span style="border-top: solid 1px #000;font-size: 12px">Authorize Signature</span><br>
                </div>
            </div>
            <br/>
            <div class="row" style="margin-top: 50px;display: block;" >
                {{-- <hr style="border-top:1px dotted black;width: 100%;height:1px;"> --}}
                <div class="col-md-12" style="text-align:center;">
                    <h3>This is computer generated voucher</h3>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
