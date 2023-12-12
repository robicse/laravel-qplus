@extends('backend.layouts.master')
@section("title","Payment Details")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Payment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a
                                href="{{route(Request::segment(1).'.dashboard')}}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Payment</li>
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
                            <h3 class="card-title">Payment Details</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.supplier-payments.index') }}">
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
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped data-table">
                                            <thead>
                                            <tr>
                                                <th>Voucher NO</th>
                                                <th>Date Time</th>
                                                <th>Warehouse</th>
                                                <th>Route</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Created By</th>
                                                <th>Description</th>
                                                <th>Approval Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($chartOfAccountTransactions  as $chartOfAccountTransaction)
                                                <tr>
                                                    <td>{{$chartOfAccountTransaction->voucher_no}}</td>
                                                    <td>{{$chartOfAccountTransaction->date_time}}</td>
                                                    <td>{{$chartOfAccountTransaction->warehouse->name}}</td>
                                                    <td>{{$chartOfAccountTransaction->warehouse->name}}</td>
                                                    <td>{{$chartOfAccountTransaction->debit}}</td>
                                                    <td>{{$chartOfAccountTransaction->credit}}</td>
                                                    <td>{{$chartOfAccountTransaction->created_by_user->name}}</td>
                                                    <td>{{$chartOfAccountTransaction->description}}</td>
                                                    <td>{{$chartOfAccountTransaction->approved_status}}</td>
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
