@extends('backend.layouts.master')
@section("title","Unit Lists")
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css" rel="stylesheet">
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
                    <h1>Units</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route(Request::segment(1).'.dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Units</li>
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
                            <h3 class="card-title">Units Lists</h3>
                            <div class="float-right">
                                @can('units-create')
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#unitModal">
                                    <i class="fa fa-plus-circle"></i>
                                    Add
                                </button>
                                @endcan
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped data-table">
                                <thead>
                                <tr>
                                    <th>#Id</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tfoot>
                                <tr>
                                    <th>#Id</th>
                                    <th>Name</th>
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

    <!-- Modal -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Unit</h5>
                    <button type="button" id="close" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close">X
                    </button>
                </div>
                <h5> @include('errors.ajaxformerror')</h5>
                <div class="modal-body">
                    {!! Form::open(['url' => Request::segment(1).'/units', 'class' => 'form', 'id' => 'ccccc']) !!}
                    {!! Form::hidden('unitid', '', ['id' => 'unitid']) !!}
                    <label for="name" class="form-label">Name *</label>
                    <div class="input-group">
                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control mb-1', 'required']) !!}
                    </div>
                    <label for="name" class="form-label">Unit Status *</label>
                    <div class="input-group">
                        {!! Form::select('status', [1 => 'Active', 0 => 'In-Active'], null, ['id' => 'status', 'class' => 'form-control mb-1', 'required', 'placeholder'=> 'Select One']) !!}
                    </div>
                </div>
                <div class="modal-footer">

                    <input type="button" id="addBtn" value="Save" class="btn btn-primary">


                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@stop

@push('js')

    <!-- sweet alert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#formerrors").hide();
            clearform();
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
                ajax: "{{ route(Request::segment(1) .'.units.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '#DeleteBtn', function () {
                if (!confirm('Are You Sure Delete ?')) return;
                $id = $(this).attr('rid');
                $info_url = "{{ url(Request::segment(1)) }}" + '/units/' + $id;
                $.ajax({
                    url: $info_url,
                    method: "DELETE",
                    type: "DELETE",
                    data: {},
                    success: function (data) {
                        if (data) {
                            Swal.fire({
                                icon: 'success',
                                title: "Delete Successfully",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $('.data-table').DataTable().ajax.reload();
                        }
                    },
                    error: function (data) {
                        // console.log(data);
                    }
                });
            });

            //create start
            $("#addBtn").click(function () {

                if ($(this).val() == 'Save') {
                    $.ajax({
                        url: "{{ route(Request::segment(1).'.units.store') }}",
                        method: "POST",
                        data: {
                            name: $("#name").val(),
                            status: $("#status").val(),
                        },
                        success: function (d) {
                            if (d.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Create Successfully",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('.data-table').DataTable().ajax.reload();
                                clearform();

                            } else {
                                $.each(d.errors, function (key, value) {
                                    $('#formerrors').show();
                                    $('#formerrors ul').append('<li>' + value +
                                        '</li>');
                                });
                            }
                        },
                        error: function (d) {
                            // alert(d.message);
                            // console.log(d);
                        }
                    });
                }
            });
            //Create end

            //Edit start
            $(document).on('click', '#EditBtn', function () {
                $id = $(this).attr('uid');
                console.log($id);
                $info_url = "{{ url(Request::segment(1)) }}" + '/units/' + $id + '/edit';
                $.get($info_url, {}, function (d) {
                    populateForm(d);
                    location.hash = "ccccc";
                    $("#unitModal").modal('show');
                });
            });
            //Edit shift end

            //Update shift
            $("#unitModal").on('click', '#addBtn', function () {
                if ($(this).val() == 'Update') {
                    $id = $("#unitid").val(),
                        $.ajax({
                            url: "{{ url(Request::segment(1)) }}" + '/units/' + $id,
                            method: "PUT",
                            type: "PUT",
                            data: {
                                name: $("#name").val(),
                                status: $("#status").val(),
                            },
                            success: function (d) {
                                if (d.success) {
                                    Swal.fire({
                                        icon: 'info',
                                        title: " Update Successfully",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    $('.data-table').DataTable().ajax.reload();
                                    clearform();
                                }
                            },
                            error: function (d) {
                                // console.log(d);
                            }
                        });
                }
            });
            //Update shift end

            //form populatede
            function populateForm(data) {
                $("#name").val(data.name);
                $("#unitid").val(data.id);
                $("#addBtn").val('Update');
            }

            function clearform() {
                $('#ccccc')[0].reset();
                $("#addBtn").val('Save');
                $("#unitModal").modal('hide');
            }

            $("#close").click(function () {
                clearform();
            });
        });
    </script>
@endpush
