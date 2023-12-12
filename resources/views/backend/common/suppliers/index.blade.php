@extends('backend.layouts.master')
@section("title","Suppliers Lists")
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
    <style>

    </style>
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Suppliers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a
                                href="{{route(Request::segment(1).'.dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">suppliers</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Supplier Lists</h3>
                            <div class="float-right">
                                @can('suppliers-create')
                                {{-- <a href="{{ @url('/backend/demo_xlsx/supplier.xlsx') }}"> <button class="btn btn-info">
                                        Download Demo <i class="fa fa-download"></i>
                                    </button></a> --}}
                                <a href="{{route(Request::segment(1).'.suppliers.create')}}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Add
                                    </button>
                                </a>
                                {{-- <a href="{{route(Request::segment(1).'.local.supplier')}}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Local
                                    </button>
                                </a>
                                <a href="{{route(Request::segment(1).'.international.supplier')}}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        International
                                    </button>
                                </a> --}}
                                @endcan
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            {{-- @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif --}}
                            {{-- <form action="{{ route(Request::segment(1) . '.supplier.supplierExcelStore') }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="supplier">Supplier Excel File Upload<span class="required">
                                                        *</span></label>
                                                <div class="d-flex">
                                                    <input type="file" id="supplier" class="form-control"
                                                        name="supplier">
                                                    <button type="submit" class=" ml-2 btn btn-primary">Submit</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form> --}}
                                <table id="example1" class="table table-bordered table-striped data-table">
                                <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
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
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
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

                ],
                ajax: "{{ route(Request::segment(1).'.suppliers.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'address',
                            name: 'address'
                        },
                        {data: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: true},
                ]
            });
        });

        function updateStatus(el,tableName){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post("{{ route(Request::segment(1).'.updateStatus')  }}",
                {_token:'{{ csrf_token() }}', id:el.value, status:status,tableName:tableName},
                function(data){
                if(data == 1){
                    toastr.success('success', 'Status updated successfully');
                }
                else{
                    toastr.danger('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endpush
