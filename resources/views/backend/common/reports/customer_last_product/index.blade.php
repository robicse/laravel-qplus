@extends('backend.layouts.master')
@section('title', 'Customer Last Product')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer Last Product Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Customer Last Product</li>
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
                            {!! Form::open(['url' => Request::segment(1) . '/customer-last-product', 'method' => 'GET']) !!}
                                <div class="row justify-content-center">
                                    {{-- @include('backend.common.reports.common_form') --}}
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Select Store:*</label>
                                            <select class="form-control" name="store_id" id="store_id" autofocus>
                                                <option value="All">All Store</option>
                                                @if(count($stores))
                                                    @foreach($stores as $store)
                                                        <option value="{{$store->id}}" {{ $store->id  == $store_id ? 'selected' : '' }} >{{$store->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Customer:*</label>
                                            <select class="form-control select2" name="customer_id" id="customer_id" autofocus required>
                                                <option value="">Select Customer</option>
                                                @if(count($customers))
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer->id}}" {{ $customer->id  == $customer_id ? 'selected' : '' }} >{{$customer->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Product:*</label>
                                            <select class="form-control select2" name="product_id" id="product_id" autofocus required>
                                                <option value="">Select Product</option>
                                                @if(count($products))
                                                    @foreach($products as $product)
                                                        <option value="{{$product->id}}" {{ $product->id  == $product_id ? 'selected' : '' }} >{{$product->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2 d-none">
                                        <label for="previewtype">
                                            <input type="radio" name="previewtype" value="htmlview" id="previewtype">
                                            Normal</label>
                                        <label for="pdfprintview">
                                            <input type="radio" name="previewtype" value="pdfview" checked id="printview"> Pdf
                                        </label>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <br>
                                            <button class="btn btn-primary  mt-2">Submit</button>
                                        </div>
                                    </div>
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

    @if($sale_products->isNotEmpty())
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Sale Lists</h3>
                            <div class="float-right">
                                @can('sales-create')
                                {{-- <a href="{{ @url('/backend/demo_xlsx/sale.xlsx') }}"> <button class="btn btn-info">
                                        Download Demo <i class="fa fa-download"></i>
                                    </button></a> --}}
                                {{-- <a href="{{URL(Request::segment(1).'/date-wise-voucher-pdf/'.$start_date.'/'.$end_date.'/'.$store_id.'/'.$previewtype)}}" target="_blank">
                                    <button class="btn btn-primary text-center" style="">Date Wise Voucher PDF</button>
                                </a> --}}
                                @endcan
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <table class="table table-bordered table-striped data-table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Voucher NO</th>
                                        <th>Product Name</th>
                                        {{-- <th>Qty</th> --}}
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale_products as $data)
                                        <tr>
                                            <td>{{ $loop->index + 01 }}</td>
                                            <td>{{ $data->id }}</td>
                                            <td>{{ Helper::getProductName($product_id)  }}</td>
                                            {{-- <td>{{ @$data->qty }}</td> --}}
                                            <td>{{ @$data->sale_price }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
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
    @endif
@stop

@push('js')
<!-- DataTables  & Plugins -->
<script src="{{asset('backend/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('backend/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('backend/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('backend/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script>
    $('.select2').select2();
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.data-table').DataTable({
            // processing: true,
            // serverSide: true,
            responsive: true,
            dom: 'Bflrtip',
            lengthMenu :
            [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],

            buttons: [
                {
                    extend: 'csv',
                    text: 'Excel',
                    exportOptions: {
                        stripHtml: true,
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        stripHtml: true,
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        stripHtml: true,
                        columns: ':visible'
                    }
                },
                'colvis'

            ]
        });
    });
</script>
@endpush
