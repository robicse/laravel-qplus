@extends('backend.layouts.master')
@section("title","Customer Show")
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
                        <li class="breadcrumb-item active">show</li>
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
                            <h3 class="card-title">Customers Details</h3>
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
                            <div class="row">
                                <div class="col-lg-6">
                                    <h6><strong>Name: </strong> {{$name=$customer->name}}</h6>
                                    <h6><strong>Arabic Name: </strong> {{$customer->arabic_name}}</h6>
                                    <h6><strong>Address: </strong> {{$customer->address}}</h6>
                                    <h6><strong>Qr Code: </strong> {{$code = $customer->customer->code}}</h6>
                                    <h6><strong>Phone: </strong> {{$customer->phone}}</h6>
                                    <h6><strong>Email: </strong> {{$customer->email}}</h6>
                                    {{-- <h6><strong>Pay Type: </strong> {{$customer->pay_type}}</h6> --}}
                                </div>
                                <div class="col-lg-6">
                                    <h6><strong>Customer NID: </strong> {{$customer->customer->nid}}</h6>
                                    <h6><strong>Auto Generate Code: </strong> {{$customer->customer->auto_generate_code}}</h6>
                                    <h6><strong>Contact Person: </strong> {{$customer->customer->contact_person}}</h6>
                                    <h6><strong>Contact Person No: </strong> {{$customer->customer->contact_person_no}}</h6>
                                    <h6><strong>Credit Limit: </strong> {{$customer->customer->credit_limit}}</h6>
                                    <h6><strong>Days Limit: </strong> {{$customer->customer->days_limit}}</h6>
                                    {{-- <h6><strong>Latitude: </strong> {{$customer->latitude}}</h6>
                                    <h6><strong>Longitude: </strong> {{$customer->longitude}}</h6> --}}
                                    <h6><strong>VAT Type: </strong> {{$vattype=$customer->customer->type}}</h6>
                                    <h6><strong>VAT NO: </strong> {{$vatnumber= $customer->customer->vat_no}}</h6>
                                    <h6><strong>Status: </strong> {{$customer->status == 0 ? 'Inactive' : 'Active'}}
                                    </h6>
                                    <h6><strong>Store Name: </strong> {{$storeinfo=$customer->customer->store_name}}</h6>
                                    {!! $info="Store : $storeinfo \n Name : $name \n Code : $code \n Vat-Type : $vattype \n Vat-No : $vatnumber"  !!}
                                    
                                </div>
                                <div class="col-md-12">
                             
                                <img class="img-fluid"   src="data:image/png;base64,{!! DNS2D::getBarcodePNG($info, 'QRCODE') !!}" alt="barcode"/>
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
