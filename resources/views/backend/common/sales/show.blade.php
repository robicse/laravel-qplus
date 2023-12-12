@extends('backend.layouts.master')
@section('title', 'Sale Details')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sale Details </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Sales</li>
                        <li class="breadcrumb-item active">details</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Sale Details</h3>
                            <div class="float-right">

                                <a href="{{ url()->previous()
                                }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    {{-- <h6><strong>Sales Info :</strong> </h6> --}}
                                    <h6><strong>Invoice NO:</strong> {{ $sale->id }}</h6>
                                    <h6><strong>Previous Due:</strong> {{$previousDue}} {{ $default_currency->symbol }} </h6>
                                    <h6><strong>Total Due:</strong> {{$previousDue+@$sale->due_amount}} {{ $default_currency->symbol }} </h6>
                                    <h6><strong>Sub Total:</strong> {{ $default_currency->symbol }} {{ @$sale->sub_total }}</h6>
                                    <h6><strong>Discount:</strong> {{ $default_currency->symbol }} {{ @$sale->discount_amount }}</h6>
                                    <h6><strong>Grand Total:</strong> {{ $default_currency->symbol }} {{ @$sale->grand_total }}</h6>

                                </div>
                                <div class="col-lg-6">
                                    {{-- <h6><strong>Customer :</strong> </h6> --}}
                                    <h6><strong> Name:</strong> {{@$sale->customer->name}}</h6>
                                    <h6> <strong> Address:</strong> {{@$sale->customer->address}}</h6>
                                    <h6><strong>VAT:</strong> {{ $default_currency->symbol }} {{ @$sale->total_vat }}</h6>
                                    <h6><strong>Created BY:</strong> {{$sale->created_by_user->name}}</h6>
                                    @php
                                        $transactions = Helper::getSalePaymentInfo($sale->id);
                                    @endphp
                                    @if(count($transactions) > 0)
                                        <h6>
                                            <strong>Payment Type</strong>
                                            <ul>
                                                @foreach($transactions as $transaction)
                                                    <li>
                                                        {{Helper::getPaymentTypeName($transaction->payment_type_id)}}
                                                        @if($transaction->payment_type_id == 3)
                                                            ( Bank Name: {{$transaction->bank_name}} )<br/>
                                                            ( Cheque Number: {{$transaction->cheque_number}} )<br/>
                                                            ( Cheque Date: {{$transaction->cheque_date}} )<br/>
                                                        @elseif($transaction->payment_type_id == 2)
                                                            ( Transaction Number: {{$transaction->transaction_number}} )<br/>
                                                        @elseif($transaction->payment_type_id == 2)
                                                            ( Note: {{$transaction->note}} )<br/>
                                                        @endif
                                                        :
                                                        Tk.{{number_format($transaction->amount,2)}} ({{$transaction->created_at}})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->

                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Sale  Details</h3>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped data-table">
                                            <thead>
                                            <tr>
                                                <th>#Id</th>
                                                <th>Product Name</th>
                                                <th>Unit</th>
                                                <th>Qty</th>
                                                <th>Price ({{ $default_currency->symbol }}) </th>
                                                <th>Discount</th>
                                                <th class="text-right">Total ({{ $default_currency->symbol }}) </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($saleDetails as $saleDetail)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ @$saleDetail->product->name }}</td>
                                                    <td>{{ @$saleDetail->unit->name }}</td>
                                                    <td>{{ @$saleDetail->qty }}</td>
                                                    <td>{{ @$saleDetail->sale_price }}</td>
                                                    <td class="text-right">{{ @($saleDetail->product_discount) }}</td>
                                                    <td class="text-right">{{ @($saleDetail->total) }}</td>

                                                </tr>
                                            @endforeach

                                            </tbody>
                                            <tfoot>
                                            {{-- <td colspan="6" class="text-right"><strong> Total:
                                                    {{ @$sale->grand_total }}</strong></td> --}}
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@stop
