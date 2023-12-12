@extends('backend.layouts.master')
@section('title', 'Sale Store Wise')
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
                    <h1>Sale Store Wise</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Sale Store Wise</li>
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
                            {!! Form::open(['url' => Request::segment(1) . '/sale-store-wise-report']) !!}
                            <div class="row justify-content-center">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Select Store:</label>
                                        <select class="form-control" name="store_id" id="store_id" autofocus>
                                            <option value="All" {{ 'All' == $store_id ? 'selected' : '' }}>All Store</option>
                                            @if(count($stores))
                                                @foreach($stores as $store)
                                                    <option value="{{$store->id}}" {{ $store->id == $store_id ? 'selected' : '' }}>{{$store->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Start Date:</label>
                                        {!! Form::date('start_date', $from, ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>End Date:</label>
                                        {!! Form::date('end_date', $to, ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <label for="previewtype">
                                        <input type="radio" name="previewtype" value="htmlview"
                                            {{ $previewtype == 'htmlview' ? 'checked' : '' }} id="previewtype">
                                        Normal</label>
                                    <label for="pdfprintview">
                                        <input type="radio" name="previewtype" value="pdfview"
                                            {{ $previewtype == 'pdfview' ? 'checked' : '' }} id="printview"> Pdf
                                    </label>
                                    {{-- <label for="previewtype">
                                        <input type="radio" name="previewtype" value="excelview" id="excelview"> Excel
                                    </label> --}}
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <br>
                                        <button class="btn btn-primary  mt-2">Submit</button>
                                        <a href="{{ url(Request::segment(1) . '/sale-store-wise-report') }}"
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
                                @if ($storeInfo)
                                    <div class="col-md-3">
                                        <h6><strong> Name: </strong>{{ @$storeInfo->name }}</h6>
                                        <h6><strong> Phone: </strong>{{ @$storeInfo->phone }}</h6>
                                        <h6><strong>Email: </strong>{{ @$storeInfo->email }}</h6>
                                        <h6><strong>Address: </strong>{{ @$storeInfo->address }}</h6>
                                    </div>
                                @else
                                    <div class="col-md-3">
                                        <h3><strong>All Store </strong></h3>
                                    </div>
                                @endif

                                <div class="col-md-3">
                                    {{-- <h3>Store Wise Sale</h3> --}}
                                </div>
                                <div class="col-md-3 text-end">
                                    <h6><strong>From Date: </strong>{{ @$from }}</h6>
                                    <h6><strong>To Date: </strong>{{ @$to }}</h6>
                                    {{-- <button id="print-button" class="btn btn-sm btn-primary"><i class="fas fa-print"></i></button> --}}
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            @if ($storeWiseSaleReports->isNotEmpty())
                                <table class="table table-bordered table-striped data-table data-table">
                                    <thead>
                                        <tr>
                                            <th>SL1</th>
                                            <th>Invoice No</th>
                                            <th>Date</th>
                                            <th>Store</th>
                                            <th>Total Vat</th>
                                            <th>Grand Total</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                            <th>Profit</th>
                                            {{-- <th>Detail</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($storeWiseSaleReports as $sale)
                                            <tr>
                                                <td>{{ $loop->index + 01 }}</td>
                                                <td>{{ $sale->id }}</td>
                                                <td>{{ $sale->voucher_date }}</td>
                                                <td>{{ @$storeInfo->name }}</td>
                                                <td>{{ $sale->total_vat }}</td>
                                                <td class="text-right">{{ $sale->grand_total }}</td>
                                                <td class="text-right">{{ $sale->paid_amount }}</td>
                                                <td class="text-right">{{ $sale->due_amount }}</td>
                                                <td class="text-right">{{ $sale->profit_amount }}</td>
                                                {{-- <td>
                                                    <a class="btn btn-warning btn-sm waves-effect" type="button"
                                                        target="_blank"
                                                        href="{{ route(\Request::segment(1) . '.sales.show', $sale->id) }}"><i
                                                            class="fa fa-eye"></i></a>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <td colspan="4"></td>
                                        <td class="text-right"><strong> Total : </strong> </td>
                                        <td class="text-right"> <strong>
                                                {{ $storeWiseSaleReports->sum('grand_total') }}</strong></td>
                                        <td colspan="4"></td>
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
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ]
            });
        });
    </script>
@endpush
