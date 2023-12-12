@extends('backend.layouts.master')
@section('title', 'Customer Ledger Lists')
@push('css')
    {{-- <link rel="stylesheet" href="{{ asset('backend/datetimepicker/css/bootstrap-datetimepicker.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer Ledger</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Customer Ledger</li>
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
                            {!! Form::open(['url' => Request::segment(1) . '/customer-ledgers']) !!}
                            <div class="row justify-content-center">
                                @include('backend.common.component.store')
                                @include('backend.common.component.customer')
                                @include('backend.common.component.start_date')
                                @include('backend.common.component.end_date')
                                <div class="col-2">
                                    <div class="form-group ">
                                        <br>
                                        <button class="btn btn-primary  mt-2" id="SUBMIT_BTN">Submit</button>
                                    </div>
                                </div>
                                <div class="col-2">&nbsp;</div>
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
    <script>
        $('.select2').select2();
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endpush
