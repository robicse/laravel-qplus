@php
    use Sohibd\Laravelslug\Generate;
    use Spatie\Permission\Models\Permission;
@endphp
@extends('backend.layouts.master')
@section('title', 'Sub Menu')
@section('content')



    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sub Menu</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Menu</a></li>
                        <li class="breadcrumb-item">Sub Menu</li>


                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->



    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                @php
                    $childModules = Helper::getChildModuleList($Modules);

                    $moduleIds = [];
                    if (count($childModules) > 0) {
                        $nestedData = [];
                        foreach ($childModules as $childModule) {
                            $nestedData[] = @$childModule->id;
                        }
                        array_push($moduleIds, $nestedData);
                    }

                @endphp


                @if (count($childModules) > 0)

                    @foreach ($childModules as $childModule)
                        @php
                            $childMenuPermission = Permission::where('module_id', @$childModule->id)
                                ->pluck('name')
                                ->first();
                        @endphp
                        @can(@$childMenuPermission)
                           <div class="col-lg-3 col-6">
                                <a href="{{ route(Request::segment(1) . '.' . @$childModule->slug . '.index') }}">
                                    <div class="info-box  bg-danger">
                                        <span class="info-box-icon text-white"><i class="{{@$childModule->icon}}"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{@$childModule->name}}</span>

                                        </div>

                                    </div>
                                </a>
                            </div>
                        @endcan
                    @endforeach

                @endif

                <!-- /.row -->
            </div>

            <!-- Main row -->

            <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
    </section>


@stop
