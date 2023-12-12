@extends('backend.layouts.master')
@section('title', 'Customers Create')
@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/css/intlTelInput.css" rel="stylesheet"
        media="screen">
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/phone-prefix.css') }}">
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
                                href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">customers</li>
                        <li class="breadcrumb-item active">create</li>
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
                            <h3 class="card-title">Customers Create</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.customers.index') }}">
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
                            {!! Form::open([
                                'route' => Request::segment(1) . '.customers.store',
                                'method' => 'POST',
                                'files' => true,
                            ]) !!}
                            @csrf
                            <input type="hidden" value="Customer" name="user_type">
                            <input type="hidden" value="Non Payable" name="pay_type">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="warehouse_id">Warehouse Id <span class="required">*</span></label>
                                    {!! Form::select('warehouse_id', $warehouses, @Auth::user()->warehouse_id ?: null, [
                                        'id' => 'warehouse_id',
                                        'class' => 'form-control',
                                        'placeholder' => 'Select One',
                                        'required','autofocus'
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="route_id">Route Id <span class="required">*</span></label>
                                    <button type="button" class="btn-primary btn-sm" onclick="showRouteForm()"><i
                                            class="fa fa-plus"></i>
                                    </button>
                                    <select class="form-control custom-select" name="route_id" id="route_id" required>

                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="store_name">Store Name <span class="required">*</span></label>

                                    {!! Form::text('store_name', null, [
                                        'id' => 'store_name',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Enter Name',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="stublish_name">Stublish Name <span class="required">*</span></label>

                                    {!! Form::text('stublish_name', null, [
                                        'id' => 'stublish_name',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Enter Name',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="name">Customer Name <span class="required">*</span></label>
                                    {!! Form::text('name', null, [
                                        'id' => 'name',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Enter Name',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="arabic_name">Customer Arabic Name <span class="required">*</span></label>
                                    {!! Form::text('arabic_name', null, [
                                        'id' => 'arabic_name',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Enter Arabic Name',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-3">
                                    <div>
                                        <label for="phone">Phone<span class="required">*</span></label>
                                    </div>
                                    <input id="phone1" type="tel" class="phone_val" name="phone"
                                        placeholder="@lang('website.phone')" value="{{ old('phone') }}" required>
                                    <input id="countyCodePrefix1" type="hidden" name="country_code" required>
                                    <span id="valid-msg1" class="hide text-success">Valid</span>
                                    <span id="error-msg1" class="hide text-danger">Invalid number</span>
                                    <span id="error-msg2" class="hide text-danger">This Phone number already Exists </span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="email">Email <span class="required">*</span></label>
                                    {!! Form::email('email', null, [
                                        'id' => 'email',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Enter email',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="nid">Customer NID <span class="required">(Optional)</span></label>
                                    {!! Form::text('nid', null, [
                                        'id' => 'nid',
                                        'class' => 'form-control',
                                         'placeholder' => 'Enter nid',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="address">Address <span class="required">(Optional)</span></label>
                                    {!! Form::text('address', null, [
                                        'id' => 'address',
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter address',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="contact_person">Contact Person <span class="required">*</span></label>

                                    {!! Form::text('contact_person', null, [
                                        'id' => 'contact_person',
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter Contact',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="contact_person_no">Contact Person No</label>

                                    {!! Form::text('contact_person_no', null, [
                                        'id' => 'contact_person_no',
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter Contact',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="type">Type <span class="required">*</span></label>

                                    {!! Form::select('type', ['Vatable' => 'Vatable', 'Non Vatable' => 'Non Vatable'], null, [
                                        'id' => 'type',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Select One',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3" id="vat_no">
                                    <label for="vat_no">VAT NO </label>

                                    {!! Form::text('vat_no', null, [
                                        'id' => 'vat_no',
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter Contact',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="credit_limit">Credit Limit <span class="required">*</span></label>
                                    {!! Form::number('credit_limit', null, [
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
                                    {!! Form::number('days_limit', null, [
                                        'id' => 'days_limit',
                                        'class' => 'form-control',
                                        'min'=>'0',
                                         'max'=>'99999999999999',
                                        'required',
                                        'placeholder' => 'Enter days limit',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="previous_balance">Previous Balance<span class="required">*</span></label>
                                    {!! Form::number('previous_balance', 0, [
                                        'id' => 'previous_balance',
                                        'class' => 'form-control',
                                        'step' => 'any',
                                        'min'=>'0',
                                         'max'=>'99999999999999',
                                        'required',
                                        'placeholder' => 'Enter previous value',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputStatus">Status <span class="required">*</span></label>
                                    {!! Form::select('status', [1 => 'Active', 0 => 'In-Active'], 1, [
                                        'id' => 'status',
                                        'class' => 'form-control',
                                        'required',
                                        'placeholder' => 'Select One',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="SUBMIT_BTN" class="btn btn-primary">Submit</button>
                            </div>
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


@push('js')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.min.js"></script> --}}
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"></script> --}}

    <script>
        $(document).ready(function() {
            $('.demo-select2').select2();
            //$('#route_area_id').hide();
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"></script>
    {{-- this section for custom js, only for this page --}}
    <script>
        function showRouteForm() {

            var page = "{{ url(Request::segment(1) . '/routes/create') }}";
            var myWindow = window.open(page, "_blank", "scrollbars=yes,width=700,height=1000,top=30");
            // focus on the popup //
            myWindow.focus();
        }
        //For register
        var telInput1 = $("#phone1"),
            errorMsg1 = $("#error-msg1"),
            errorMsg2 = $("#error-msg2"),
            validMsg1 = $("#valid-msg1");

        telInput1.intlTelInput({

            allowExtensions: true,
            formatOnDisplay: true,
            autoFormat: true,
            autoHideDialCode: true,
            autoPlaceholder: true,
            defaultCountry: "auto",
            ipinfoToken: "yolo",

            nationalMode: false,
            numberType: "MOBILE",

            preferredCountries: ['bn', 'sa', 'ae', 'qa', 'om', 'bh', 'kw', 'ma'],
            preventInvalidNumbers: true,
            separateDialCode: true,
            initialCountry: "auto",

            geoIpLookup: function(callback) {
                $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    console.log(resp);
                    var countryCode = (resp && resp.country) ? resp.country : "";

                    callback(countryCode);
                });
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"

        });

        var reset = function() {
            telInput1.removeClass("error");
            errorMsg1.addClass("hide");
            validMsg1.addClass("hide");
        };

        telInput1.blur(function() {
            var countyCodePrefix1 = $(".flag-container").find('.selected-dial-code').html();
            console.log(countyCodePrefix1)
            if (countyCodePrefix1 != '+880') {
                $("#division_area").hide();
                $("#district_area").hide();
            } else {
                $("#division_area").show();
                $("#district_area").show();
            }
            const string = $('.phone_val').val();
            let info = (string.slice(0, 1));
            if (info == 0) {
                $('.phone_val').val(string.slice(1));
            }
            var phone_val = $('.phone_val').val();
            console.log(phone_val);
            $.post('{{ route('super_admin.check.phone') }}', {
                _token: '{{ csrf_token() }}',
                phone_val: phone_val
            }, function(data) {
                // console.log(data);
                if (data == 1) {
                    toastr.warning('This phone number already exist!');
                }
            });

            $("#countyCodePrefix1").val(countyCodePrefix1);

            reset();
            if ($.trim(telInput1.val())) {
                if (telInput1.intlTelInput("isValidNumber")) {
                    validMsg1.removeClass("hide");
                } else {
                    telInput1.addClass("error");
                    errorMsg1.removeClass("hide");
                }
            }
        });

        telInput1.on("keyup change", reset);

        $('#vat_type').on('change', function(event) {
            var type = $('#vat_type').val();
            if (type == 'Vatable') {
                $('#vat_no').show();
                $("#vat_no input").prop('required', true);
            } else {
                $('#vat_no').hide();
                $("#vat_no input").prop('required', false);
            }
        });

        /* $('#route_id').on('change', function (event) {
                var route_id = $('#route_id').val();
                if(route_id){
                    $('#route_area_id').show();
                }else{
                    $('#route_area_id select').val(null).trigger('change');
                    $('#route_area_id').hide();

                }

        }); */
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
                    $.each(data, function(key, value) {
                        $('#route_id').append('<option value="' + value.id + '">' + value.name +
                            '</option>');
                    });
                },
                error: function(err) {
                    console.log(err);
                }
            })

        });


        var loggedIn = @json(Auth::user()->user_type);
        if (loggedIn !== 'Super Admin') {
            $.ajax({
                url: "{{ URL(Request::segment(1) . '/get-route-warehouse') }}",
                method: 'GET',
                data: {
                    warehouse_id: $('#warehouse_id').val()
                },
                success: function(data) {
                    //console.log(data);
                    $('#route_id').append('<option value=""> Select One </option>');
                    $.each(data, function(key, value) {
                        $('#route_id').append('<option value="' + value.id + '">' + value.name +
                            '</option>');
                    });
                },
                error: function(err) {
                    console.log(err);
                }
            })
        };
    </script>
@endpush
