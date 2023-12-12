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
                    <h1> Admin Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
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
    <?php
        $stores = Helper::getStoreList({{ Auth::user()->store_id }});
    ?>
    @if(!empty($stores))
        @foreach($stores as $store)
        <?php
        $getStoreReportCount = Helper::getStoreReportCount($store->id);
        ?>
            <h2 class="text-center bold pt-4">STORE: {{ $store->name }}</h2>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>TK.{{ number_format($getStoreReportCount['purchaseAmount'], 2, '.', '') }}</h3>
                                    <p>Total Purchase Amount</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="{{ route(Request::segment(1) . '.purchases.index') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>TK.{{ number_format($getStoreReportCount['saleAmount'], 2, '.', '') }}</h3>
                                    <p>Total Sales Amount</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="{{ route(Request::segment(1) . '.sales.index') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>TK.{{ number_format($getStoreReportCount['purchaseReturnAmount'], 2, '.', '') }}</h3>
                                    <p>Total Purchase Return Amount</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="{{ route(Request::segment(1) . '.customers.index') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>TK.{{ number_format($getStoreReportCount['saleReturnAmount'], 2, '.', '') }}</h3>
                                    <p>Total Sale Return Amount</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="{{ route(Request::segment(1) . '.suppliers.index') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    @endif
    <h2 class="text-center bold pt-4">Report Area</h2>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $getReportCount['productCount'] }}</h3>
                            <p>Total Product</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route(Request::segment(1) . '.products.index') }}" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $getReportCount['userCount'] }}</h3>
                            <p>Total User</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{ route(Request::segment(1) . '.users.index') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $getReportCount['customerCount'] }}</h3>
                            <p>Total Customer</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route(Request::segment(1) . '.customers.index') }}" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $getReportCount['supplierCount'] }}</h3>
                            <p>Total Supplier</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route(Request::segment(1) . '.suppliers.index') }}" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>
            {{-- <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Weekly Purchase</h3>
                                <a href="{{ route(Request::segment(1) . '.purchase.local.supplier') }}">View Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span
                                        class="text-bold text-lg">{{ $default_currency->symbol }} {{ number_format($purchases_line_chart_data['line_total_purchases'], 2, '.', ',') }}</span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-muted">Since last week</span>
                                </p>
                            </div>
                            <div class="position-relative mb-4">
                                <canvas id="purchase-visitors-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> This Week
                                </span>
                                <span>
                                    <i class="fas fa-square text-gray"></i> Last Week
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Monthly Purchases</h3>
                                <a href="{{ route(Request::segment(1) . '.purchase.local.supplier') }}">View Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span
                                        class="text-bold text-lg">{{ $default_currency->symbol }} {{ number_format($purchases_bar_chart_data['bar_total_purchases'], 2, '.', ',') }}</span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-muted">Since last month</span>
                                </p>
                            </div>
                            <div class="position-relative mb-4">
                                <canvas id="purchases-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> This year
                                </span>
                                <span>
                                    <i class="fas fa-square text-gray"></i> Last year
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Weekly Sales</h3>
                                <a href="{{ route(Request::segment(1) . '.sales.index') }}">View Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span
                                        class="text-bold text-lg">{{ $default_currency->symbol }} {{ number_format($sales_line_chart_data['line_total_sales'], 2, '.', ',') }}</span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-muted">Since last week</span>
                                </p>
                            </div>
                            <div class="position-relative mb-4">
                                <canvas id="visitors-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> This Week
                                </span>
                                <span>
                                    <i class="fas fa-square text-gray"></i> Last Week
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Monthly Sales</h3>
                                <a href="{{ route(Request::segment(1) . '.sales.index') }}">View Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span
                                        class="text-bold text-lg">{{ $default_currency->symbol }} {{ number_format($sales_bar_chart_data['bar_total_sales'], 2, '.', ',') }}</span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-muted">Since last month</span>
                                </p>
                            </div>
                            <div class="position-relative mb-4">
                                <canvas id="sales-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> This year
                                </span>
                                <span>
                                    <i class="fas fa-square text-gray"></i> Last year
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
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
