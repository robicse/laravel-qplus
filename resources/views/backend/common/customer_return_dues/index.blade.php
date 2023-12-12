{{-- @dd('fff'); --}}
@extends('backend.layouts.master')
@section('title', 'Customer Return Due Lists')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css" rel="stylesheet">
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
                    <h1>Customer Return Due Lists</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Customer Return Due Lists</li>
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
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Customer Return Due Lists</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.customer-payments.create') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Customer Return Due Refund
                                    </button>
                                </a>
                            </div>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body table-responsive">

                            <table class="table table-bordered data-table">
                                <thead>
                                    <tr>
                                        <th>Voucher</th>
                                        <th>Date</th>
                                        <th>Store</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Comments</th>
                                        <th>Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables  & Plugins -->
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
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
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
                    'colvis',
                ],
                ajax: "{{ route(Request::segment(1) . '.customer-return-dues.index') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'store.name',
                        name: 'store.name'
                    },
                    {
                        data: 'customer.name',
                        name: 'customer.name'
                    },
                    {
                        data: 'customer.phone',
                        name: 'customer.phone'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'comments',
                        name: 'comments'
                    },
                    {
                        data: 'created_by_user',
                        name: 'created_by_user'
                    }
                ]
            });
        });
    </script>
@endpush

@push('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"
        integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script>
        function updateStatus(id, sel) {
            console.log(id);
            var approved_status = sel.value
            console.log('approved_status', approved_status)
            $.ajax({
                url: "{{ URL(Request::segment(1) . '/transaction-status-update') }}",
                method: "get",
                data: {
                    id: id,
                    approved_status: approved_status
                },

                success: function(res) {
                    //$(("#id_" + id)).html(approved_status);
                    Swal.fire('Successfully Updated!');
                    window.reload();
                },
                error: function(err) {
                    console.log(err)
                }
            })
        }
    </script>
@endpush
