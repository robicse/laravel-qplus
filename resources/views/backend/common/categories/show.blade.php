@extends('backend.layouts.master')
@section("title","Reem")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Warehouses</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">{{request()->path()}}</li>
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
                            <h3 class="card-title">Warehouses Details</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.warehouses.index') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div>
                                <h6>Name: {{$warehouses->name}}</h6>
                                <h6>Code: {{$warehouses->code}}</h6>
                                <h6>Phone: {{$warehouses->phone}}</h6>
                                <h6>Email: {{$warehouses->email}}</h6>
                                <h6>Address: {{$warehouses->address}}</h6>
                                <h6>Latitude: {{$warehouses->lat}}</h6>
                                <h6>Longitude: {{$warehouses->lng}}</h6>
                                <h6>Status: {{$warehouses->status}}</h6>
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
