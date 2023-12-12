@extends('backend.layouts.master')
@section('title', 'Product Price Status')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product Price Status Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Product Price Status</li>
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
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {!! Form::open(['url' => Request::segment(1) . '/date-wise-voucher', 'method' => 'GET']) !!}
                                <div class="row justify-content-center">
                                    {{-- @include('backend.common.reports.common_form') --}}
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Select Store:*</label>
                                            <select class="form-control" name="store_id" id="store_id" autofocus>
                                                <option value="All">All Store</option>
                                                @if(count($stores))
                                                    @foreach($stores as $store)
                                                        <option value="{{$store->id}}" {{ $store->id  == $store_id ? 'selected' : '' }} >{{$store->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Start Date:*</label>
                                            {!! Form::date('start_date', $start_date, ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>End Date:*</label>
                                            {!! Form::date('end_date', date('Y-m-d'), ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="col-2 d-none">
                                        <label for="previewtype">
                                            <input type="radio" name="previewtype" value="htmlview" id="previewtype">
                                            Normal</label>
                                        <label for="pdfprintview">
                                            <input type="radio" name="previewtype" value="pdfview" checked id="printview"> Pdf
                                        </label>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <br>
                                            <button class="btn btn-primary  mt-2">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Sale Lists</h3>
                            <div class="float-right">
                                @can('sales-create')
                                {{-- <a href="{{ @url('/backend/demo_xlsx/sale.xlsx') }}"> <button class="btn btn-info">
                                        Download Demo <i class="fa fa-download"></i>
                                    </button></a> --}}
                                <a href="{{URL(Request::segment(1).'/date-wise-voucher-pdf/'.$start_date.'/'.$end_date.'/'.$store_id.'/'.$previewtype)}}" target="_blank">
                                    <button class="btn btn-primary text-center" style="">Date Wise Voucher PDF</button>
                                </a>
                                @endcan
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <table class="table table-bordered table-striped data-table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Voucher NO</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Qty</th>
                                        <th class="text-right">Sub Total</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Grand Total</th>
                                        <th class="text-right">Paid Amount</th>
                                        <th class="text-right">Due Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dateWiseVoucherReports as $data)
                                        <tr>
                                            <td>{{ $loop->index + 01 }}</td>
                                            <td>{{ @$data->voucher_date }}</td>
                                            <td>{{ @$data->id }}</td>
                                            <td>{{ @$data->customer->name }}</td>
                                            <td>{{ @$data->customer->phone }}</td>
                                            <td>{{ @$data->total_quantity }}</td>
                                            <td class="text-right"> {{ @$data->sub_total }}</td>
                                            <td class="text-right"> {{ @$data->discount_amount }}</td>
                                            <td class="text-right">{{ $data->grand_total }}</td>
                                            <td class="text-right"> {{ @$data->paid_amount }}</td>
                                            <td class="text-right"> {{ @$data->due_amount }}</td>
                                            <td>
                                                <span  class="d-inline-flex">
                                                    <a href="{{route(Request::segment(1).'.sales.show',$data->id)}}" class="btn btn-warning btn-sm waves-effect" target="_blank">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{URL(Request::segment(1).'/sales-invoice-pdf/'.$data->id)}}" class="btn btn-info  btn-sm float-left" style="margin-left: 5px" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Voucher NO</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Qty</th>
                                        <th class="text-right">Sub Total</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Grand Total</th>
                                        <th class="text-right">Paid Amount</th>
                                        <th class="text-right">Due Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
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

@push('js')
@endpush
