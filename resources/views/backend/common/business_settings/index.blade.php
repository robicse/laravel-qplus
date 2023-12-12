@extends('backend.layouts.master')
@section("title","Business Setting Edit")
@push('css')
    <link rel="stylesheet" href="{{asset('backend/css/custom.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Business Setting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route(Request::segment(1).'.dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Business Setting</li>
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
                            <h3 class="card-title">Business Setting Edit</h3>
                            <div class="float-right">

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <label>Title</label>
                            <form id="title">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$title->id}}">
                                    <input type="text" class="form-control" name="value" value="{{$title->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>Company Contact No</label>
                            <form id="company_contact_no">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$company_contact_no->id}}">
                                    <input type="number" class="form-control" name="value" value="{{$company_contact_no->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>Company Address</label>
                            <form id="company_address">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$company_address->id}}">
                                    <input type="text" class="form-control" name="value" value="{{$company_address->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>Company Email</label>
                            <form id="company_email">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$company_email->id}}">
                                    <input type="email" class="form-control" name="value" value="{{$company_email->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>
                            <label>Company Logo</label>
                            <span> <img src="{{asset($company_logo->value)}}" alt=""  height="30px" width="30px"></span>
                            <form id="company_logo" enctype="multipart/form-data" method="post">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$company_logo->id}}">
                                    <input type="file" id="logo" name="logo" class="form-control">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            {{-- <label>Trade Licence Image</label>
                            <span> <img src="{{asset($trade_license->value)}}" alt=""  height="30px" width="30px"></span>
                            <form id="trade_license" enctype="multipart/form-data" method="post">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$trade_license->id}}">
                                    <input type="file" id="trade_license_image" name="trade_license_image" class="form-control">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>Trade License Expired Date</label>
                            <form id="trade_license_expired_date">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$trade_license_expired_date->id}}">
                                    <input type="date" class="form-control" name="value" value="{{$trade_license_expired_date->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>



                            <label>Vat NO</label>
                            <form id="vat_no">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$vat_no->id}}">
                                    <input type="number" class="form-control" name="value" value="{{$vat_no->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>Vat Percent</label>
                            <form id="vat_percent">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$vat_percent->id}}">
                                    <input type="number" class="form-control" name="value"
                                           value="{{$vat_percent->value}}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>System Default Currency</label>
                            <form id="system_default_currency">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id"
                                           value="{{$system_default_currency->id}}">
                                    <select class="form-control custom-select" name="value">
                                        @foreach($currencies as $currency)
                                            <option
                                                value="{{$currency->id}}" {{$currency->id == $system_default_currency->value ? 'selected':''}}>{{$currency->symbol}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form>

                            <label>Time Zone</label>
                            <form id="time_zone">
                                <div class="input-group mb-3">
                                    <input type="hidden" class="form-control" name="id" value="{{$time_zone->id}}">
                                    <select class="form-control" name="value">
                                        <option value="Asia/Riyadh" {{ $time_zone->value == 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh</option>
                                        <option value="Asia/Dhaka" {{ $time_zone->value == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </form> --}}
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
    <script type="text/javascript">
        //for vat update
        /* var APP_URL = {!! json_encode(url('/')) !!}
        console.log(APP_URL); */

        $(document).ready(function () {
            $("#vat_no").submit(function (event) {
                event.preventDefault();
                var $form = $(this);
                var $inputs = $form.find("input, select, button, textarea");
                var serializedData = $form.serialize();
                console.log(serializedData)
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                    data: $('#vat_no').serialize(),
                    success: function (data) {
                        //console.log(data);
                        if (data == 1) {
                            toastr.success('Vat Value Updated Successfully');
                        } else {
                            toastr.error('something went wrong');
                        }
                    }
                });
            })

            $("#vat_percent").submit(function (event) {
                console.log('aaa')
                event.preventDefault();
                var $form = $(this);
                var $inputs = $form.find("input, select, button, textarea");
                var serializedData = $form.serialize();
                console.log(serializedData)
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                    data: $('#vat_percent').serialize(),
                    success: function (data) {
                        //console.log(data);
                        if (data == 1) {
                            toastr.success('Vat Value Updated Successfully');
                        } else {
                            toastr.error('something went wrong');
                        }
                    }
                });
            })
        });

        //for title
        $("#title").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#title').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Service Charge Value Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //system_default_currency
        $("#system_default_currency").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#system_default_currency').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Service Charge Value Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //unit_price_variant
        $("#unit_price_variant").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#unit_price_variant').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Unit Price Value Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company_contact_no
        $("#company_contact_no").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#company_contact_no').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Company contact no Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company_contact_no
        $("#company_address").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#company_address').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Company address Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company Logo
        $("#company_logo").submit(function (event) {
            event.preventDefault();

            var formData = new FormData($('#company_logo')[0]);
            console.log(formData);

           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Company logo Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        $("#trade_license").submit(function (event) {
            event.preventDefault();

            var formData = new FormData($('#trade_license')[0]);
            console.log(formData);

           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Trade License Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company_contact_no
        $("#trade_license_expired_date").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            //console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#trade_license_expired_date').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Trade License Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //tax payer name update
        $("#taxpayer_name").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            //console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#taxpayer_name').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Taxpayer Name Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company_contact_no
        $("#effective_registration_date").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            //console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#effective_registration_date').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Effective Registration Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        $("#cr_license_contract_no").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#cr_license_contract_no').serialize(),
                success: function (data) {
                    //console.log(data);
                    if (data == 1) {
                        toastr.success('Cr license No Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //tax payer name update
        $("#taxperiod").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            //console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#taxperiod').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Taxperiod Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company email update
        $("#company_email").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            //console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#company_email').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Company Email Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })

        //company email update
        $("#time_zone").submit(function (event) {
            event.preventDefault();
            var $form = $(this);
            var $inputs = $form.find("input, select, button, textarea");
            var serializedData = $form.serialize();
            //console.log(serializedData)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ URL(Request::segment(1) . '/business-settings-update') }}",
                data: $('#time_zone').serialize(),
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Time Zone Updated Successfully');
                    } else {
                        toastr.error('something went wrong');
                    }
                }
            });
        })



    </script>
@endpush
