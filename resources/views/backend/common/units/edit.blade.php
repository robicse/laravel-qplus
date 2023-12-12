@extends('backend.layouts.master')
@section("title","Unit Edit")
@push('css')
    <link rel="stylesheet" href="{{asset('backend/css/custom.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Unit</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route(Request::segment(1).'.dashboard')}}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Unit</li>
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
                            <h3 class="card-title">Unit Edit</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1).'.units.index') }}">
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
                            <form action="{{route(Request::segment(1).'.units.update',$unit->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">Name <span class="required">*</span></label>
                                    <input type="text" id="name" class="form-control" name="name"
                                           value="{{$unit->name}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="inputStatus">Status</label>
                                    <select class="form-control custom-select" name="status">
                                        <option value="1" {{$unit->status == 1 ? 'selected':''}}>Active</option>
                                        <option value="0" {{$unit->status == 0 ? 'selected':''}}>Inactive</option>
                                    </select>
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
