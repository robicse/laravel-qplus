@extends('backend.layouts.master')
@section("title","Customers Edit")
@push('css')
    <link rel="stylesheet" href="{{asset('backend/css/custom.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a
                                href="{{route(Request::segment(1).'.dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">customers</li>
                        <li class="breadcrumb-item active">edit</li>
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
                            <h3 class="card-title">Customer Edit</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.customers.index') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
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
                             {!! Form::model($customer, array('route' =>[Request::segment(1).'.customers.update', $customer->id],'method'=>'PUT','files'=>true)) !!}

                                <input type="hidden" value="Customer" name="user_type">
                                <input type="hidden" value="Non Payable" name="pay_type">
                                {{Form::hidden('warehouse_id', $customer->warehouse_id,['class' => 'form-control']) }}
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="warehouse_id">Warehouse Id <span class="required">*</span></label>
                                        {!! Form::select('warehouse_id', $warehouses, $customer->warehouse_id, [
                                            'id' => 'warehouse_id',
                                            'class' => 'form-control',
                                            'placeholder' => 'Select One',
                                            'disabled'
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="route_id">Route Id <span class="required">*</span></label>
                                        <select class="form-control custom-select" name="route_id" id="route_id" required>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="store_name">Store Name <span class="required">*</span></label>
                                        {!! Form::text('store_name', $customer->customer->store_name, [
                                            'id' => 'store_name',
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => 'Enter Name',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="stublish_name">Stublish Name <span class="required">*</span></label>
                                        {!! Form::text('stublish_name', $customer->customer->stublish_name, [
                                            'id' => 'stublish_name',
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => 'Enter Name',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="name">Customer Name <span class="required">*</span></label>
                                        {!! Form::text('name', $customer->name, [
                                            'id' => 'name',
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => 'Enter Name',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="arabic_name">Customer Arabic Name <span
                                                class="required">*</span></label>
                                       {!! Form::text('arabic_name', $customer->arabic_name, [
                                            'id' => 'arabic_name',
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => 'Enter Arabic Name',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="phone">Phone <span class="required">*</span></label>
                                        <div class="row">
                                            <input type="text" class="form-control col-md-3" name="country_code"
                                                   value="{{$customer->country_code}}" readonly>
                                            <input type="number" class="form-control col-md-8" name="phone"
                                                   value="{{$customer->phone}}" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="email">Email <span class="required">*</span></label>
                                        {!! Form::email('email', $customer->email, [
                                            'id' => 'email',
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => 'Enter email',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="nid">Customer NID<span
                                                class="required">Optional</span></label>
                                     {!! Form::text('nid', $customer->customer->nid, [
                                            'id' => 'nid',
                                            'class' => 'form-control',

                                            'placeholder' => 'Enter nid',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="address">Address <span class="required">(Optional)</span></label>
                                        {!! Form::text('address', $customer->address, [
                                            'id' => 'address',
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter address',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="auto_generate_code">Auto Generate Code<span
                                                class="required">*</span></label>

                                         {!! Form::text('auto_generate_code', $customer->customer->auto_generate_code, [
                                            'id' => 'auto_generate_code',
                                            'class' => 'form-control',
                                            'readonly',
                                            'placeholder' => 'Enter address',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="contact_person">Contact Person <span class="required">*</span></label>
                                       {!! Form::text('contact_person', $customer->customer->contact_person, [
                                            'id' => 'contact_person',
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Contact',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="contact_person_no">Contact Person No</label>

                                        {!! Form::text('contact_person_no', $customer->customer->contact_person_no, [
                                            'id' => 'contact_person_no',
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Contact',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="type">Type</label>
                                         {!! Form::select('type', ['Vatable' => 'Vatable', 'Non Vatable' => 'Non Vatable'], $customer->customer->type, ['id' => 'type', 'class' => 'form-control', 'required','placeholder' => 'Select One']) !!}
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="vat_no">VAT NO </label>
                                        {!! Form::text('vat_no', $customer->customer->vat_no, [
                                            'id' => 'vat_no',
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Contact',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="credit_limit">Credit Limit <span class="required">*</span></label>
                                        {!! Form::number('credit_limit', $customer->customer->credit_limit, [
                                            'id' => 'credit_limit',
                                            'class' => 'form-control',
                                            'step' => 'any',
                                            'min'=>'0',
                                            'max'=>'99999999999999',
                                            'required',
                                            'placeholder' => 'Enter credit limit',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="days_limit">Days Limit <span class="required">*</span></label>
                                         {!! Form::number('days_limit', $customer->customer->days_limit, [
                                            'id' => 'days_limit',
                                            'class' => 'form-control',
                                            'required',
                                            'step' => 'any',
                                            'min'=>'0',
                                             'max'=>'99999999999999',
                                            'placeholder' => 'Enter days limit',
                                        ]) !!}
                                    </div>


                                    <div class="form-group col-md-3">
                                        <label for="inputStatus">Status <span class="required">*</span></label>
                                        {!! Form::select('status', [1 => 'Active', 0 => 'In-Active'], $customer->status, ['id' => 'status', 'class' => 'form-control', 'required','placeholder' => 'Select One']) !!}
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
@stop

@push('js')

    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.min.js"></script>--}}
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"></script>--}}

    <script>

        $(document).ready(function() {
             $('.demo-select2').select2();
         });



            $.ajax({
                url: "{{ URL(Request::segment(1) . '/get-route-warehouse-edit') }}",
                method: 'GET',
                data: {
                    warehouse_id:  $('#warehouse_id').val()
                },
                success: function(data) {
                    //$('#route_id').html(data.data.routeOptions)
                    var id ="{{@$customer->route_id}}";
                    //console.log(id);
                    $('#route_id').append('<option value=""> Select One </option>');
                    $.each(data, function (key, value) {
                            if (value.id == id) {
                                $('#route_id').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            } else {
                                //console.log(value);
                                $('#route_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            }
                        });
                },
                error: function(err) {
                    console.log(err);
                }
            });

         $('#warehouse_id').change(function() {
            //console.log('kkk')
            $('#route_id').html('')
            var warehouse_id = $(this).val();
            $.ajax({
                url: "{{ URL(Request::segment(1) . '/get-route-warehouse') }}",
                method: 'GET',
                data: {
                    warehouse_id: warehouse_id
                },
                success: function(data) {
                    //console.log(data);
                    $('#route_id').append('<option value=""> Select One </option>');
                    $.each(data, function (key, value) {
                        $('#route_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                },
                error: function(err) {
                    console.log(err);
                }
            })

            });

    </script>

@endpush
