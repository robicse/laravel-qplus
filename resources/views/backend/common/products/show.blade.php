<strong> @extends('backend.layouts.master') </strong>@section("title","Supplier Show")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Supplers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a
                                href="{{route(Request::segment(1).'.dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">supplers</li>
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
                            <h3 class="card-title">Supplers Details</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.suppliers.index') }}">
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
                                    <h6><strong> Name: </strong> {{$supplier->name}}</h6>
                                    <h6><strong> Type: </strong> {{$supplier->supplier->type}}</h6>
                                    <h6><strong> Address: </strong> {{$supplier->address}}</h6>
                                    <h6><strong> Code: </strong> {{$supplier->supplier->code}}</h6>
                                    <h6><strong> Vat No: </strong> {{$supplier->supplier->vat_no}}</h6>
                                    <h6><strong> Phone: </strong> {{$supplier->phone}}</h6>
                                    <h6><strong> Supplier Location : </strong>{{$supplier->supplier->supplier_location }}</h6>
                                    <h6><strong> Comercial Registration No: </strong>{{$supplier->supplier->comercial_registration_no  }}</h6>
                                    <h6><strong>Warehouse: </strong> {{@$supplier->warehouse->name}}</h6>
                                    <h6><strong>Pay Type: </strong> {{$supplier->pay_type}}</h6>
                                </div>
                                <div class="col-lg-6">
                                    
                                    <h6><strong> Bank Accounts Details: </strong>{{$supplier->supplier->bank_accounts_details  }}</h6>
                                    <h6><strong> Product Groups: </strong>{{$supplier->supplier->bank_accounts_details   }}</h6>
                                    <h6><strong> Credit Limit: </strong> {{$supplier->supplier->credit_limit}}</h6>
                                    <h6><strong> Days Limit: </strong> {{$supplier->supplier->days_limit}}</h6>
                                    <h6><strong> Payment Terms: </strong> {{$supplier->supplier->payment_terms}}</h6>
                                    <h6><strong> Email: </strong> {{$supplier->email}}</h6>
                                   {{--  <h6><strong> Latitude: </strong> {{$supplier->lat}}</h6>
                                    <h6><strong> Longitude: </strong> {{$supplier->lng}}</h6> --}}
                                    <h6><strong> Status: </strong> {{$supplier->status == 0 ? 'Inactive' : 'Active'}}
                                    </h6>
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
