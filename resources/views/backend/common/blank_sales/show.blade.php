@extends('backend.layouts.master')
@section('title', 'Blank Sale Details')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Blank Sale Details </h1>
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
                            <h3 class="card-title">Blank Sale Details</h3>
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
                                    <h6><strong>Invoice NO:</strong> {{ $blankSale->id }}</h6>
                                    <h6><strong>Created BY:</strong> {{$blankSale->created_by_user->name}}</h6>
                                    <h6> <strong> Total Quantity:</strong> {{@$blankSale->total_quantity}}</h6>
                                </div>
                                <div class="col-lg-6">
                                    {{-- <h6><strong>Customer :</strong> </h6> --}}
                                    <h6><strong> Name:</strong> {{@$blankSale->customer->name}}</h6>
                                    <h6> <strong> Address:</strong> {{@$blankSale->customer->address}}</h6>
                                    <h6> <strong> Grand Total:</strong> {{@$blankSale->grand_total}}</h6>
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
                            <h3 class="card-title">Blank Sale  Details</h3>

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
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($blankSaleDetails as $data)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ @$data->product->name }}</td>
                                                    <td>{{ @$data->unit->name }}</td>
                                                    <td>{{ @$data->qty }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
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
