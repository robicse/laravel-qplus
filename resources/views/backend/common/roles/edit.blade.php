@extends('backend.layouts.master')
@section('title', 'Role Edit')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Role</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Role</li>
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
                            <h3 class="card-title">Role Update</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.roles.index') }}">
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
                            {!! Form::model($role, [
                                'route' => [Request::segment(1) . '.roles.update', $role->id],
                                'method' => 'PATCH',
                                'files' => true,
                            ]) !!}
                            <form role="form" action="{{route(Request::segment(1).'.roles.update',$role->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Role Name</label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{$role->name}}"
                                               readonly required>
                                    </div>
                                    <div class="form-group">
                                        <h3>Permissions</h3>
                                        <p class="bg-primary pl-3 p-2">
                                            <input type="checkbox" id="checkAll"> <span class="text-light text-bold">By a click you can select all </span>
                                        </p>
                                        @foreach($getParentAndChildModuleList as $module)
                                            <div class="row">
                                                @php
                                                    $actions =Helper::getModulePermissionActionByModuleId($module->id);
                                                    $checkedCount = 0;
                                                    if(count($actions) > 0){
                                                        foreach($actions as $action){
                                                            $checkedCount += in_array($action->id, $rolePermissions) ? 1 : 0;
                                                        }
                                                    }
                                                @endphp
                                                <div class="col-md-3">{{$module->name}}</div>
                                                <div class="col-md-2">
                                                    @if((count($actions) > 0 ))
                                                        <label for="checkAllModuleAction_{{$module->id}}"><input type="checkbox" onclick="checkUncheck({{$module->id}})" id="checkAllModuleAction_{{$module->id}}" {{ ( (count($actions) > 0 ) && ($checkedCount == count($actions)) ) ? 'checked' : '' }} > Select All</label>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    @if(count($actions) > 0)
                                                        @foreach($actions as $action)
                                                            <p>
                                                                <input type="checkbox" class="action_{{$module->id}}" id="checkModuleAction" name="permission[]"  value="{{$action->id}}" {{ in_array($action->id, $rolePermissions) ? 'checked' : '' }} > {{$action->name}}
                                                            </p>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                                <!-- /.card-body -->
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
    <script>
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        function checkUncheck(id){
            console.log('id',id);
            if ($("#checkAllModuleAction_"+id).is(':checked')) {
                console.log("checked");
                $('.action_'+id+':input:checkbox').each(function() {
                    this.checked = true;
                });
            }else{
                console.log("unchecked");
                $('.action_'+id+':input:checkbox').each(function() {
                    this.checked = false;
                });
            }
        }
    </script>
    @endpush
