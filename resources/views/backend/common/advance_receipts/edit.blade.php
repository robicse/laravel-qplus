@extends('backend.layouts.master')
@section('title', 'Advance Recept Update')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Advance Recept</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Advance Recept</li>
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
                            @can('suppliers-create')
                            <h3 class="card-title">Advance Recept Update</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.advance-receipts.index') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
                            @endcan
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {!! Form::model($advance_receipt, [
                                'route' => [Request::segment(1) . '.advance-receipts.update', $advance_receipt->id],
                                'method' => 'PATCH',
                                'files' => true,
                            ]) !!}
                            @include('backend.common.advance_receipts.form')
                            {!! Form::close() !!}
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
@section('calx')
    <script src="{{ asset('backend/jquery-calx-sample-2.2.8.min.js') }}"></script>
@endsection
@push('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"
    integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
        $('.select2').select2();

        $(function() {
            $('#note').hide();
            $('#transaction_number').hide();
            $('#bank_name').hide();
            $('#cheque_number').hide();
            $('#cheque_date').hide();
            $('#payment_type_id').change(function(){
                if($('#payment_type_id').val() == '3') {
                    $('#bank_name').show();
                    $('#cheque_number').show();
                    $('#cheque_date').show();
                    $('#transaction_number').hide();
                    $('#note').hide();
                }else if($('#payment_type_id').val() == '2') {
                    $('#transaction_number').show();
                    $('#bank_name').hide();
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                    $('#note').hide();
                }else if($('#payment_type_id').val() == '4') {
                    $('#note').show();
                    $('#bank_name').hide();
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                    $('#transaction_number').hide();
                } else {
                    $('#note').val('');
                    $('#note').hide();
                    $('#transaction_number').hide();
                    $('#bank_name').val('');
                    $('#bank_name').hide();
                    $('#cheque_number').val('');
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                }
            });
        });
    </script>
@endpush

