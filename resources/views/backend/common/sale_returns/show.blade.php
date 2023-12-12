@extends('backend.layouts.master')
@section("title","Van Return Details")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Van Return</h1>
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
                            <h3 class="card-title">Van Return</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.sale-returns.index') }}">
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
                                    <h6><strong>Invoice NO:</strong> {{ $SaleReturn->id }}</h6>
                                    <h6><strong>Store :</strong> {{ $store->name }}</h6>
                                    <h6><strong>Address :</strong> {{ $store->address }}</h6>
                                </div>
                                <div class="col-lg-6">
                                    <h6><strong>Name: {{@$SaleReturn->customer->name}}</strong></h6>
                                    <h6><strong>Address: {{@$SaleReturn->customer->address}}</strong></h6>
                                    <h6><strong>Grand Total:</strong> {{ @$SaleReturn->grand_total }}</h6>
                                    <h6><strong>Invoice Due:</strong> {{ @$SaleReturn->invoice_due }}</h6>
                                    <h6><strong>Refundable Amount:</strong> {{ @$SaleReturn->refundable_amount }}</h6>
                                    <h6><strong>Refund Amount:</strong> {{ @$SaleReturn->refund_amount }}</h6>
                                    <h6><strong>Due Amount:</strong> {{ @$SaleReturn->due_amount }}</h6>
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
                            <h3 class="card-title">Van Return Details</h3>

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
                                                    <th>Qty</th>
                                                    <th>Already Return Qty</th>
                                                    <th>Total</th>
                                                    <th>Profit Minus</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach( $SaleReturnDetails as $SaleReturnDetail)
                                                <tr>
                                                    <td>{{($loop->index+1)}}</td>
                                                    <td>{{@$SaleReturnDetail->product->name}}</td>
                                                    <td>{{@$SaleReturnDetail->qty}}</td>
                                                    <td>{{Helper::getAlreadySaleReturnQty($SaleReturn->sale_id,$SaleReturnDetail->product_id)}}</td>
                                                    <td>{{@$SaleReturnDetail->amount}}</td>
                                                    <td>{{@$SaleReturnDetail->profit_minus}}</td>
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
