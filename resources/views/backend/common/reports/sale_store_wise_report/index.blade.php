@extends('backend.layouts.master')
@section('title', 'Sale Store Wise')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sale Store Wise Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Sale Store Wise</li>
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
                            {!! Form::open(['url' => Request::segment(1) . '/sale-store-wise-report']) !!}
                            <div class="row justify-content-center">
                                @include('backend.common.reports.common_form')
                                <div class="col-2">
                                    <div class="form-group">
                                        <br>
                                        <button class="btn btn-primary  mt-2">Submit</button>
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

@push('js')
@endpush
