@extends('backend.layouts.master')
@section("title","Purchase Details")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Purchase</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a
                                href="{{route(Request::segment(1).'.dashboard')}}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">purchases</li>
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
                            <h3 class="card-title">Purchase</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.purchases.index') }}">
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
                                    <h6><strong>Invoice NO:</strong> {{$purchase->id}}</h6>
                                    <h6><strong>Purchase From:</strong> {{$purchase->purchase_from}}</h6>
                                    <h6><strong>Supplier:</strong> {{$purchase->supplier->name}}</h6>
                                    <h6><strong>Store:</strong> {{$purchase->store->name}}</h6>
                                    <h6><strong>Sub Total:</strong> {{$purchase->sub_total}}</h6>
                                    <h6><strong>Created BY:</strong> {{$purchase->created_by_user->name}}</h6>
                                </div>
                                <div class="col-lg-6">
                                    <h6><strong>Vat:</strong> {{ $default_currency->symbol }} {{$purchase->total_vat}}</h6>
                                    <h6><strong>Grand Total:</strong> {{ $default_currency->symbol }} {{$purchase->grand_total}}</h6>
                                    <h6><strong>Paid:</strong> {{ $default_currency->symbol }} {{$purchase->paid_amount}}</h6>
                                    <h6><strong>Due:</strong> {{ $default_currency->symbol }} {{$purchase->due_amount}}</h6>
                                    @if($purchase->payment_type_id)
                                    <h6><strong>Payment Type:</strong> {{Helper::getPaymentTypeName($purchase->payment_type_id)}}</h6>
                                    @endif
                                    {{-- @if($purchase->payment_type_id)
                                    <h6><strong>Purchase Type:</strong> {{Helper::getPaymentTypeName($purchase->payment_type_id)}}</h6>
                                    @endif --}}
                                    @php
                                        $transactions = Helper::getPurchasePaymentInfo($purchase->id);
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
                            <h3 class="card-title">Stock Details</h3>
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
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Qty</th>
                                                <th>Total ({{ $default_currency->symbol }})</th>
                                                <th>Product Total ({{ $default_currency->symbol }})</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($purchaseDetails  as $purchaseDetail)
                                                <tr>
                                                    <td>{{$purchaseDetail->id}}</td>
                                                    <td>{{$purchaseDetail->product->name}}</td>
                                                    <td>{{$purchaseDetail->product->unit->name}}</td>
                                                    <td>{{$purchaseDetail->qty}}</td>
                                                    <td>{{$purchaseDetail->purchase_price}}</td>
                                                    <td>{{$purchaseDetail->product_total}}</td>
                                                </tr>

                                            @endforeach
                                            </tbody>
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
