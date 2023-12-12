@extends('backend.layouts.master')
@section('title', 'Store Current Stock Report Lists')
@push('css')
    <style>
        p {
            font-size: 14px;
            margin-bottom: 5px !important;
        }

        td {
            font-size: 14px;
        }

        select option {
            font-size: 14px;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 10px 30px !important;
            }
        }

        @media print {
            footer {
                display: none;
            }

            #print-button {
                display: none;
            }

            #mySelect {
                display: none;
            }

            .main-cards {
                padding: 0;
            }
        }
    </style>
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Store Curent Stock Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Store Current Stock Report</li>
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
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {!! Form::open(['url' => Request::segment(1) . '/multiple-store-current-stock-report']) !!}
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select Store:</label>
                                        {!! Form::select('store_id[]', $stores, $store_ids, [
                                            'class' => 'form-control select2',
                                            'id' => 'store_id',
                                            'required',
                                            'multiple',
                                        ]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select Product:</label>
                                        {{-- {!! Form::select('product_id[]', $products, $product_ids, [
                                            'class' => 'form-control select2',
                                            'placeholder' => 'Select One',
                                            'id' => 'product_id',
                                            'required',
                                            'multiple',
                                        ]) !!} --}}
                                        <select class="form-control select2" name="product_id[]" id="product_id" required multiple autofocus>
                                            <option value="All" {{ $product == 'All' ? 'selected' : '' }}>All Product</option>
                                            @if(count($products))
                                                @foreach($products as $data)
                                                    <option value="{{$data->id}}" {{ in_array($data->id, $product_ids)  ? 'selected' : '' }}>{{$data->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group ">
                                        <br>
                                        <button class="btn btn-primary  mt-2">Submit</button>
                                        <a href="{{ url(Request::segment(1) . '/multiple-store-current-stock-report') }}"
                                            class="btn btn-primary" type="button" style="margin-top:8px;">Reset</a>
                                    </div>
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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <div class="row justify-content-center">
                                {{-- @if ($warehouseInfo)
                                    <div class="col-md-3">
                                        <h6><strong> Name: </strong>{{ @$warehouseInfo->name }}</h6>
                                        <h6><strong> Arabic Name: </strong>{{ @$warehouseInfo->arabic_name }}</h6>
                                        <h6><strong> Phone: </strong>{{ @$warehouseInfo->phone }}</h6>
                                        <h6><strong> Contact Person: </strong>{{ @$warehouseInfo->contact_person }}</h6>
                                        <h6><strong>Email: </strong>{{ @$warehouseInfo->email }}</h6>
                                        <h6><strong>Address: </strong>{{ @$warehouseInfo->address }}</h6>
                                    </div>
                                @endif
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-3 text-end">
                                    <h6><strong>From Date: </strong>{{ @$from }}</h6>
                                    <h6><strong>To Date: </strong>{{ @$to }}</h6>
                                </div> --}}
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            @if ($products->isNotEmpty())
                                <table class="table table-bordered table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Product Name</th>
                                            <th>Unit (Base)</th>
                                            <th>Store Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($products as $data)
                                            <tr>
                                                <td class="text-right">{{ $loop->index + 01 }}</td>
                                                <td class="text-left">{{ @$data->name }}</td>
                                                <td class="text-left">{{ @$data->unit->name }}</td>
                                                <td class="text-right">
                                                    {{-- {{ @$data->store->name }} --}}
                                                    <table>
                                                        @php
                                                            $stores = \App\Models\Store::whereIn('id',$store_ids)->get();
                                                        @endphp
                                                        @if(count($stores) > 0)
                                                            @foreach($stores as $store)
                                                            @php
                                                                $current_stock = \App\Helpers\Helper::storeProductCurrentStock($store->id, $data->id);
                                                                $total += $current_stock;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $store->name }}</td>
                                                                <td>
                                                                    {{ $current_stock }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @endif
                                                    </table>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <td colspan="2"></td>
                                        <td class="text-right"><strong> Total : </strong> </td>
                                        <td class="text-right"> <strong>
                                            {{ $total }}</strong></td>
                                    </tfoot>
                                </table>
                            @else
                                <div>
                                    <h2 class="text-center">No Data found</h2>
                                </div>
                            @endif
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
    <script src="{{ asset('backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                dom: 'Bflrtip',
                paginate: false,

                buttons: [{
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ]
            });
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.select2').select2();
            $('#print-button').on('click', function() {
                window.print();
                return false; // why false?
            });
        })
    </script>
@endpush
