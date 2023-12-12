@extends('backend.layouts.master')
@section('title', 'Loss Profit Store Wise')
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
                    <h1>Loss Profit Store Wise</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Loss Profit Store Wise</li>
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
                            {!! Form::open(['url' => Request::segment(1) . '/loss-profit-store-wise-report']) !!}
                            <div class="row justify-content-center">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Select Store:</label>
                                        {!! Form::select('store_id', $stores, $store_id, [
                                            'class' => 'form-control select2',
                                            'placeholder' => 'Select One',
                                            'id' => 'store_id',
                                        ]) !!}
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
                                        <a href="{{ url(Request::segment(1) . '/loss-profit-store-wise-report') }}"
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
                                    {{-- <h3>Store Wise Loss Profit</h3> --}}
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
                            @if ($storeWiseLossProfitReports->isNotEmpty())
                                <table class="table table-bordered table-striped data-table data-table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Invoice No</th>
                                            <th>Date</th>
                                            <th>Loss/Profit</th>
                                            {{-- <th>Detail</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($storeWiseLossProfitReports as $sale)
                                            <tr>
                                                <td>Sale</td>
                                                <td>{{ $sale->id }}</td>
                                                <td>{{ $sale->voucher_date }}</td>
                                                <td class="text-right">{{ $sale->profit_amount }}</td>
                                                {{-- <td>
                                                    <a class="btn btn-warning btn-sm waves-effect" type="button"
                                                        target="_blank"
                                                        href="{{ route(\Request::segment(1) . '.sales.show', $sale->id) }}"><i
                                                            class="fa fa-eye"></i></a>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                        @foreach ($storeWiseLossProfitReportsReturn as $saleReturn)
                                            <tr>
                                                <td>Sale Return</td>
                                                <td>{{ $saleReturn->id }}</td>
                                                <td>{{ $saleReturn->return_date }}</td>
                                                <td class="text-right">{{ $saleReturn->profit_minus_amount }}</td>
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
                                        <td colspan="2"></td>
                                        <td class="text-right"><strong> Total : </strong> </td>
                                        <td class="text-right"> <strong>
                                                {{ number_format($storeWiseLossProfitReports->sum('profit_amount') - $storeWiseLossProfitReportsReturn->sum('profit_minus_amount'), 2) }}</strong></td>
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
