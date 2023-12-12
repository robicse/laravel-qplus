@php
    use Sohibd\Laravelslug\Generate;
    use Spatie\Permission\Models\Permission;
    $getReportCount = Helper::getReportCount();
@endphp
@extends('backend.layouts.master')
@section('title', 'Dashboard')
{{-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {
        packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable({{ Js::from($result) }});
        var options = {
            title: 'My Daily Activities',
            is3D: true,
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
    }
</script> --}}
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Salesman Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Salesman</a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                        {{-- <li class="breadcrumb-item active"> {!! Form::select('warehouse_id', $warehouse, Auth::user()->warehouse_id, [
                            'class' => '',
                            'id' => 'warehouse_id',
                            'disabled',
                        ]) !!}</li> --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>
    {{-- <h2 class="text-center bold">Keyboard Shortcut</h2>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <a href="#">
                        <div class="info-box  bg-danger">
                            <div class="info-box-content">
                                <span class="info-box-text">Customer Create => Altr+C</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a href="#">
                        <div class="info-box  bg-danger">
                            <div class="info-box-content">
                                <span class="info-box-text">Supplier Create => Altr+S</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a href="#">
                        <div class="info-box  bg-danger">
                            <div class="info-box-content">
                                <span class="info-box-text">Purchase Stock => Altr+W</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a href="#">
                        <div class="info-box  bg-danger">
                            <div class="info-box-content">
                                <span class="info-box-text">Sale/Voucher => Altr+W</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section> --}}
    <h2 class="text-center bold pt-4">Menu Area</h2>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @php
                    $modules = Helper::getCollapseAndParentModuleList();
                    $segment = Request::segment(1);
                @endphp
                @if (count($modules) > 0)
                    @foreach (@$modules as $module)
                        @php
                            $mainMenuPermission = Permission::where('module_id', @$module->id)
                                ->pluck('name')
                                ->first();
                        @endphp
                        @can($mainMenuPermission)
                            @if ($module->parent_menu === 'Parent')
                                <div class="col-lg-3 col-6">
                                    <a href="{{ url(Request::segment(1) . '/' . $module->slug) }}">
                                        <div class="info-box  bg-danger">
                                            <span class="info-box-icon text-white"><i class="{{ @$module->icon }}"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ @$module->name }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @elseif($module->parent_menu === 'Collapse')
                                @php
                                    $childModules = Helper::getChildModuleList(@$module->name);
                                    $slugList = Helper::getChildModuleSlugList(@$module->name,Request::segment(1));
                                    if(in_array(Request::segment(2),@$slugList)){
                                        $active = 'found';
                                    }else{
                                        $active = 'not found';
                                    }

                                    $moduleIds = [];
                                    if (count($childModules) > 0){
                                        $nestedData = [];
                                        foreach($childModules as $childModule){
                                            $nestedData[]=@$childModule->id;
                                        }
                                        array_push($moduleIds, $nestedData);
                                    }
                                    $collapseChildMenuPermission = Helper::collapseChildMenuPermission($moduleIds[0]);
                                @endphp

                                @can($collapseChildMenuPermission)
                                    @php  $slug=Generate::Slug($module->slug); @endphp
                                    <div class="col-lg-3 col-6">
                                        <a href="{{ url(Request::segment(1) . '/sub-menu/' . $module->id) }}">
                                            <div class="info-box  bg-danger">
                                                <span class="info-box-icon text-white"><i class="{{ @$module->icon }}"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">{{ @$module->name }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endcan
                            @else

                            @endif
                        @endcan
                    @endforeach
                @endif
            </div>
        </div>
    </section>

@stop
@push('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        // dashboard3.js

    </script>
@endpush
