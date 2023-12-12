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
                        font-size: 12px;
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
                        font-size: 10px !important;
                        padding-bottom: 5px;
                    }
                </style>
                <div class="panel-body">
                    {{-- <h5 style="text-align: center">
                        <strong>Invoice</strong>
                    </h5> --}}
                    @include('backend.common.reports.header')
                    @include('backend.common.reports.date_time',['invoice_type'=>'Money Receipt','invoice_no'=>@$saleReturn->id])
                    <br/>
                    <br/>

                    <table>
                        <tr>
                            <td class="strong smallstrong">Customer Name: {{ @$transactions[0]->customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="strong small">Mobile: {{ @$transactions[0]->customer->phone }}</td>
                        </tr>
                        <tr>
                            <td class="strong small">Address: {{ @$transactions[0]->customer->address }}</td>
                        </tr>
                        <tr>
                            <td class="strong small">Money Receipt Number: {{ @$transactions[0]->invoice_no }}</td>
                        </tr>
                    </table>

                    <br/>
                    <br/>

                    <table>
                        <thead>
                            <tr class="strong" style="background: #eceff4;">
                                <th width="10%">SL</th>
                                <th width="60%"> Payment Type</th>
                                <th width="10%" class="text-right"> Amount </th>
                                <th width="20%"> Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="strong">
                            @foreach($transactions as $key => $transaction)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td style="text-align: center;">
                                        {{@$transaction->payment_type->name}}<br/>
                                        @if(@$transaction->payment_type->name == 'Cheque')
                                            Cheque Number: {{$transaction->cheque_number}}<br/>
                                            bank Name: {{$transaction->bank_name}}<br/>
                                            Cheque Date: {{$transaction->cheque_date}}<br/>
                                        @elseif(@$transaction->payment_type->name == 'Condition')
                                            Note: {{$transaction->note}}<br/>
                                        @elseif(@$transaction->payment_type->name == 'Card' || @$transaction->payment_type->name == 'Online')
                                            Note: {{$transaction->transaction_number}}<br/>
                                        @endif
                                        {{-- {{number_format($transaction->amount,2)}} --}}
                                    </td>
                                    <td class="text-right currency">{{number_format($transaction->amount,2)}}</td>
                                    <td>{{ @$transaction->comments }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <table style="width: 40%;margin-right:auto;padding-top: 45px;" class="text-left sm-padding small ">
                        <tbody>
                            <tr>
                                <th class="strong" style="text-align: left">Total: {{ number_format(@$transactions->sum('amount'), 2) }} /-</td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%;padding-top: 5px;" class="sm-padding small strong pt-2">
                        <tbody>
                            <tr>
                                <th class="strong" style="text-align: left">In Word :{{ucwords($digit->format(@$transactions->sum('amount'),2))}} Taka Only</th>
                            </tr>

                        </tbody>
                    </table>
                    <table style="width: 100%;padding-top: 100px;margin-top: 100px;" class="sm-padding small strong pt-2">
                        <tbody>
                            <tr>
                                <th class="strong" style="text-align: left"><span style="border-top: 1px solid #ddd;">Authorized Signature</span></th>
                            </tr>
                        </tbody>
                    </table>


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
            <strong>فاتورة ضريبية</strong><br/>
            <strong>Invoice</strong>
            <h1><i>Water Fine BD</i></h1>
            <h5 style="text-align: center">
                <img style="height:100px;width:auto" src="{{ asset(@$saleReturn->store->logo)}}" alt="printing logo"
                        class="card-img-top">
            </h5>
            <h5 style="text-align: center">Water Fine BD</h5>

        </div>


        <div style="float: left;display: inline-block;">
            Invoice :{{ @$saleReturn->id }}<br>
            Date & Time  <span dir="ltr" lang="AR">:</span> {{$dateTime= @$saleReturn->created_at }}<br>
            Store:  {{ Helper::getStoreName(@$saleReturn->store_id) }}<br>
            Cust. Name :{{ @$saleReturn->customer->name }}<br>
            Previous Due: {{$previousDue}} <br>
            Total Due: {{$previousDue+@$saleReturn->due_amount}}
        </div>
        @php

        // $totalVat=(@$saleReturn->total_vat);
        //     $totalAmount=(@$saleReturn->grand_total);
        // $displayQRCodeAsBase64 = GenerateQrCode::fromArray([
        //     new Seller($printheadiline), // seller name
        //     new TaxNumber(@SalePrintSetting()->vat_number),
        //     new InvoiceDate(@$saleReturn->created_at),
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
                        {{ $default_currency->symbol }} {{ @$saleReturn->sub_total }}
                    </p>

                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$saleReturn->total_vat }}
                    </p>
                    <p style="padding-bottom: 5px;">
                        {{ $default_currency->symbol }} {{ @$saleReturn->discount }}
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
                        {{ $default_currency->symbol }} {{ @$saleReturn->grand_total }}
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
                        {{ $default_currency->symbol }} {{ @$saleReturn->paid }}
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
{{--                <img width="170mm" height="30mm" src="data:image/png;base64,{!! DNS1D::getBarcodePNG(@$saleReturn->id, 'C39') !!}" />--}}
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

