@php
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
@endphp
@if($pagesize=='a4')
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

<!-- Printable area end -->
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4></h4>
                </div>
            </div>
            <div id="printArea">
                <style>
                    .panel-body {
                        min-height: 1000px !important;
                        font-size: 12px !important;
                        /* font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; */
                        font-family: Camber;
                        font-weight: inherit;
                    }
                    .invoice {
                        border-collapse: collapse;
                        width: 100%;
                    }

                    .invoice th {
                        /*border-top: 1px solid #000;*/
                        /*border-bottom: 1px solid #000;*/
                        border-bottom: 1px dotted #000;
                    }

                    .invoice td {
                        text-align: center;
                        font-size: 14px;
                        border-bottom: 1px dotted #000;
                    }

                    .invoice-logo{
                        margin-right: 0;
                    }

                    .invoice-logo > img, .invoice-logo > span {
                        float: right !important;
                    }

                    .invoice-to{
                        /* border: 1px solid black; */
                        margin: 0;
                    }

                    @page {
                        size: A4;

                        margin: 16px 50px !important;
                    }

                    .footer_div {
                        position:absolute;
                        bottom: 0 !important;
                        /*border-top: 1px solid #000000;*/
                        width:100%;
                        font-size: 14px !important;
                        padding-bottom: 15px;
                    }
                </style>
                <div class="panel-body">
                    {{-- <h5 style="text-align: center">
                        <strong>Invoice</strong>
                    </h5> --}}
                    @include('backend.common.reports.header')
                    @include('backend.common.reports.date_time',['invoice_type'=>'Sale Invoice','invoice_no'=>@$sale->id,'date'=>@$sale->voucher_date,'created_at'=>@$sale->created_at])
                    <br/>
                    <br/>
                    <div style="">
                        <table>
                            <tr>
                                <td class="small">Customer: {{ @$sale->customer->name }}
                                    </td>
                            </tr>
                            <tr>
                                <td class="small">Address: {{ @$sale->customer->address ?: $sale->customer->email }}</td>
                            </tr>
                            <tr>
                                <td class="small">Customer Phone: {{ @$sale->customer->phone }}</td>
                            </tr>
                        </table>
                    </div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <table class="invoice">
                        <thead>
                            <tr style="background-color: #dddddd">
                                <th style="width: 15%">
                                    SL No.<br/>
                                </th>
                                <th style="width: 25%">
                                    Description<br/>
                                </th>
                                <th style="width: 15%">
                                    Uint<br/>
                                </th>
                                <th style="width: 15%">
                                    Qty<br/>
                                </th>
                                <th style="width: 15%">
                                    Unit Price<br/>
                                </th>
                                <th style="width: 15%">
                                    Total Price<br/>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sum_sub_total = 0;
                            @endphp
                            @foreach($saleProducts as $key => $sales_info)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td style="text-align: left">
                                        {{ Str::limit(@$sales_info->product->name, 50, '..') }}<br/>
                                        {{ Str::limit(@$sales_info->product->arabic_name, 50, '..') }}
                                    </td>
                                    <td>{{@$sales_info->unit->name}}</td>
                                    <td>{{@$sales_info->qty}}</td>
                                    <td style="text-align: center">{{number_format(@$sales_info->sale_price,2)}}</td>
                                    <td style="text-align: center">
                                        {{number_format(@$sales_info->qty*@$sales_info->sale_price,2)}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="barcode_details">
                        <div style="width: 100%;">
                            <div style="width: 50%; float:left; text-align:center;">
                                <table style="width: 100%;margin-right:auto;margin-top: 40px;margin-bottom: 40px;">
                                    <tbody>
                                        <tr>
                                            <th class="text-right" style="text-align: left">In Word :{{ucwords($digit->format($sale->grand_total,2))}} taka only</th>
                                        </tr>
                                        @if(count($transactions) > 0)
                                            <tr>
                                                <th class="text-right" style="text-align: left">
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
                                        @if($sale->hc_voucher_number)
                                            <tr style="margin-top: 60px;margin-bottom: 200px !important; padding-bottom: 200px">
                                                <th class="text-right " style="text-align: left;padding-top:10px;">HC Voucher Number: {{ @$sale->hc_voucher_number }}</th>
                                            </tr>
                                        @endif
                                        <tr style="margin-top: 60px;margin-bottom: 200px !important; padding-bottom: 200px">
                                            <th class="text-right " style="text-align: left;padding-top:10px;">Comments: {{ @$sale->comments }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div style="width: 40%; float:right; text-align:right;">
                                <table style="width: 80%;margin-right:auto;padding-top:20px;margin-top:20px;" class="text-right sm-padding small">
                                    <tbody>
                                        <tr>
                                            <th class=" text-right">Sub Total:</th>
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
                                            <th class="text-right">Advance Total:</th>
                                            <td class="currency">{{ number_format(@$sale->advance_minus_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Paid Amount:</th>
                                            <td class="currency">{{ number_format(@$sale->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Due Amount:</th>
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

                        </div>
                        <table style="width: 100%;padding-top: 45px;" class="sm-padding small  pt-2">
                            <tbody>
                                {{-- <tr>
                                    <th class="text-right" style="text-align: right">In Word :{{ucwords($digit->format(@$refundable_amount,2))}} taka only</th>
                                </tr> --}}
                                <tr>
                                    <th class="text-left" style="text-align: left">Comments2:</th>
                                </tr>
                            </tbody>
                        </table>
                    @endif

                    <br/>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div class="row" style="margin-top: 150px;">
                        <div class="row" style="margin-top: 50px;display: block;">
                            <div style="text-align: left;float: left;width: 65%;margin-left: 10px; display: inline-block;">
                                <span style="border-bottom: solid 1px #000;text-align: center;width:400px;margin-top: -42px;font-size: 16px !important">Declaration</span><br>
                                <p style="font-size: 16px !important">We declare that this voucher shows the actual price of the goods<br/> described and that all particulars are true and correct</p>
                            </div>
                            <div style="text-align:right;float: right;width: 30%;margin-right: 10px; display: inline-block;">
                                <span style="border-top: solid 1px #000;font-size: 16px !important">Authorize Signature</span><br>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div class="row" style="margin-top: 70px;display: block;" >
                        <div class="col-md-12" style="text-align:center;">
                            <h3>This is computer generated voucher</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>

<script type="text/javascript">

    window.addEventListener("load", window.print());
</script>




    {{-- a4 end --}}
@elseif($pagesize=='80mm')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS-invoice</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            outline: none;
        }


        body {
            /* font-family: sans-serif; */
            font-family: Camber;
            font-size: 12px;
        }

        .main-invoice {
            width: 302.36px;
            padding: 40px 10px;
            margin: auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
        }

        .address {
            text-align: center;
        }

        .address span {
            display: block;
        }

        .border {
            border-bottom: 1px solid #000;
            padding-top: 5px;
            margin-bottom: 5px;
        }

        .w-50 {
            width: 50%;
        }

        .d-flex {
            display: flex;
        }

        .text-center {
            text-align: center
        }

        .text-right {
            text-align: right
        }

        .text-left {
            text-align: left
        }
    </style>
</head>

<body>
    <div class="main-invoice">
        <div class="logo">
            <>فاتورة ضريبية</><br/>
            <>Invoice</>
            <h1><i>Water Fine BD</i></h1>
            <h5 style="text-align: center">
                <img style="height:100px;width:auto" src="{{ asset(@$sale->store->logo)}}" alt="printing logo"
                        class="card-img-top">
            </h5>
            <h5 style="text-align: center">Water Fine BD</h5>

        </div>


        <div style="float: left;display: inline-block;">
            Invoice :{{ @$sale->id }}<br>
            Date & Time  <span dir="ltr" lang="AR">:</span> {{$dateTime= @$sale->created_at }}<br>
            Store:  {{ Helper::getStoreName(@$sale->store_id) }}<br>
            Cust. Name :{{ @$sale->customer->name }}<br>
            Previous Due: {{$previousDue}} <br>
            Total Due: {{$previousDue+@$sale->due_amount}}
        </div>
        @php

        // $totalVat=(@$sale->total_vat);
        //     $totalAmount=(@$sale->grand_total);
        // $displayQRCodeAsBase64 = GenerateQrCode::fromArray([
        //     new Seller($printheadiline), // seller name
        //     new TaxNumber(@SalePrintSetting()->vat_number),
        //     new InvoiceDate(@$sale->created_at),
        //     new InvoiceTotalAmount($totalAmount),
        //     new InvoiceTaxAmount($totalVat)

        // ])->render();

        @endphp

        <p>
            <img src="{{$displayQRCodeAsBase64}}" alt="QR Code" style="display: block; margin-left: auto;margin-right: auto;height: 150px;width: auto;"/>
        </p>
        <div class="border"></div>

        <div class="d-flex" style="margin-top: 10px;">
            <p style="width: 5%">
                <b>SL </b>
                <b> عدد </b>
            </p>
            <p style="width: 35%">
                <b>&nbsp; &nbsp; &nbsp;Description</b>
                <b>&nbsp; &nbsp; &nbsp; يصف</b>
            </p>
            <p style="width: 10%">
              <b>U/M</b>  <br>
               <b>يو/م</b>
            </p>
            <p style="width: 20%" class="text-right">
                <b>MRP</b> {{ $default_currency->symbol }}<br>
                <b>كمية</b>
            </p>
            <p style="width: 10%" class="text-right">
                <b>Qty</b><br>
                <b> سعر الوحدة</b>
            </p>
            <p style="width: 20%" class="text-right">
                <b>Price</b> {{ $default_currency->symbol }}<br>
                <b>سعر الوحدة</b>
            </p>
        </div>
        <div class="border"></div>

        @foreach ($saleProducts as $sales_info)
            <div class="d-flex" style="margin-top: 10px;">
                <p style="width:5%">
                    {{ $loop->index + 1 }}
                </p>
                <p style="width: 35%; font-size:10px">
                    {{ Str::limit(@$sales_info->product->name, 50, '..') }} <br>
                    {{ Str::limit(@$sales_info->product->arabic_name, 50, '..') }}
                </p>
                <p style="width:10%">
                   {{@$sales_info->unit->name}}
                </p>
                <p style="width:25%" class="text-center">
                    {{ @$sales_info->outer_sale_price }}
                </p>
                <p style="width: 5%" class="text-center">
                    {{ @$sales_info->qty }}
                </p>
                <p style="width:20%" class="text-right">
                    {{ @$sales_info->product_total }}

                </p>
            </div>
        @endforeach

        <div style="margin-left: auto; width: 95%; margin-top: 10px;">

            <div class="d-flex">
                <div class="w-50">
                    <p style="padding-bottom: 5px;">
                        <b>
                            Sub Total (المجموع الفر):
                        </b>
                    </p>
                    <p style="padding-bottom: 10px;">
                        <b>
                            (+) VAT ( خصم ):
                        </b>
                    </p>
                    <p style="padding-bottom: 10px;">
                        <b>
                            (-) Discount ( خصم ):
                        </b>
                    </p>
                </div>

                <div class="w-50" style="text-align: right">
                    <div class="border"></div>
                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$sale->sub_total }}
                    </p>

                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$sale->total_vat }}
                    </p>
                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$sale->discount }}
                    </p>
                </div>
            </div>
            <div class="border"></div>
            <div class="d-flex">
                <div class="w-50">
                    <p style="padding-bottom: 5px;">
                        <b>
                            Net Payble
                        </b>
                    </p>

                </div>
                <div class="w-50" style="text-align: right">
                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$sale->grand_total }}
                    </p>

                </div>
            </div>

            <div class="d-flex">
                <div class="w-50">
                    <p style="padding-bottom: 5px;">
                        <b>
                            Paid
                        </b>
                    </p>

                </div>
                <div class="w-50" style="text-align: right">
                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$sale->paid }}
                    </p>

                </div>
            </div>

        </div>
        <div class="border"></div>



        <p style="margin-top: 10px; border-bottom: 1px dotted #000; padding-bottom: 5px; text-align: center">
            Terms & Conditions
        </p>

        {{-- <p style="padding-top: 5px;text-align: center">
             {{ @SalePrintSetting()->print_first_condition }}
            <br>
             {{ @SalePrintSetting()->print_second_condition }}

        </p> --}}
{{--        <div class="barcode" style="margin: 20px 0; text-align: center;">--}}
{{--            <h1 style="">--}}
{{--                <img width="170mm" height="30mm" src="data:image/png;base64,{!! DNS1D::getBarcodePNG(@$sale->id, 'C39') !!}" />--}}
{{--            </h1>--}}
{{--        </div>--}}

        {{-- <p style="font-size: 16px; font-weight: 700; text-align: center;">
            {{ @SalePrintSetting()->returnpollicy }}
        </p> --}}
{{--        <div class="border"></div>--}}
{{--        <p style="text-align: center;">--}}
{{--            Software By: StarIT &copy; (2014-{{ date('Y') }})--}}
{{--            <br />Tel- (+88) 01700000000 <br />--}}
{{--        </p>--}}

    </div>
</body>

{{-- for default  --}}
@else

@endif



<script>
    window.print();
</script>

