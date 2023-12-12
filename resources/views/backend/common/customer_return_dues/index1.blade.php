@extends('backend.layouts.master')
@section('title', 'Customer Due Lists')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer Due</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Customer Due</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row overflow-hidden">
                <div class="col-12 col-lg-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Customer Due </h3>
                            <div class="float-right">

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped data-table">
                                {{-- <thead style="background-color:#ea707e"> --}}
                                <thead>
                                    <tr>
                                        <th>Inv Date</th>
                                        <th>Inv No</th>
                                        <th>Warehouse</th>
                                        <th>Van</th>
                                        <th>Customer</th>
                                        <th>Cus Code</th>
                                        <th>Cus Tel</th>
                                        <th>Total ({{ $default_currency->symbol }})</th>
                                        <th> Paid ({{ $default_currency->symbol }})</th>
                                        <th> Due ({{ $default_currency->symbol }})</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
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
    <div class="modal fade" id="CustomerDuePayModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Customer Due Payment</h5>
                    <button type="button" id="close" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X
                    </button>
                </div>
                <h5> @include('errors.ajaxformerror')</h5>
                <div class="modal-body">
                    {!! Form::open(['url' => Request::segment(1) . '/customer-due', 'class' => 'form', 'id' => 'ccccc']) !!}
                    {!! Form::hidden('saledueid', '', ['id' => 'saledueid']) !!}
                    {!! Form::hidden('warehouse_id', '', ['id' => 'warehouse_id']) !!}

                    <label for="due" class="form-label"> Due *</label>
                    <div class="input-group">
                        {!! Form::text('due', null, [
                            'id' => 'due',
                            'class' => 'form-control mb-1',
                            'required',
                            'readonly',
                        ]) !!}
                    </div>
                    <label for="paid" class="form-label"> Payment Amount *</label>
                    <div class="input-group">
                        {!! Form::number('paid', null, [
                            'id' => 'paid',
                            'class' => 'form-control mb-1',
                            'required',
                            'step'=>'any',
                            'min'=>'0',
                            'max'=>'999999999999999',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        <label for="payment_id">Select Payment<span class="required"> *</span></label>
                        {!! Form::select('payment_id', $paymentTypes, null, [
                            'id' => 'payment_id',
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => 'Select One',
                        ]) !!}
                    </div>
                    <div class="form-group d-none" id="bankName">
                        <label for="bank_id">Bank Name <span class="required">*</span></label>
                        <select class="form-control select2" name="code" id="bank_id" style="width: 100%">
                            <option value="">Select One *</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Note<span class="required"> </span></label>
                        {!! Form::text('note', null, ['id' => 'note', 'class' => 'form-control']) !!}

                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" id="addBtn" value="Receive" class="btn btn-primary">
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@stop
@push('js')
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
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
                dom: 'Bflrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],

                buttons: [{
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
                ajax: "{{ route(Request::segment(1) . '.customer-due.index') }}",
                columns: [{
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse'
                    },
                    {
                        data: 'van',
                        name: 'van'
                    },
                    {
                        data: 'customer.name',
                        name: 'customer.name'
                    },
                    {
                        data: 'customers.code',
                        name: 'customers.code'
                    },
                    {
                        data: 'customer.phone',
                        name: 'customer.phone'
                    },
                    {
                        data: 'grand_total',
                        name: 'grand_total'
                    },
                    {
                        data: 'paid',
                        name: 'paid'
                    },
                    {
                        data: 'due',
                        name: 'due'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });


            $(document).on('click', '#AddPayment', function() {
                $id = $(this).attr('sid');
                $info_url = "{{ url(Request::segment(1)) }}" + '/customer-due/' + $id + '/edit';
                $.ajax({
                    url: $info_url,
                    method: "GET",
                    success: function(data) {
                        if (data) {
                            console.log('success', data);
                            Swal.fire({
                                icon: 'info',
                                title: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            populateForm(data);
                            location.hash = "ccccc";
                            $("#CustomerDuePayModal").modal('show');

                        }
                    },
                    error: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: "Sorry All Ready Paid",
                            timer: 2000,
                            showConfirmButton: false,
                        });

                    }
                });
            });

            function populateForm(data) {
                $("#saledueid").val(data.saleinfo.id);
                $("#warehouse_id").val(data.saleinfo.warehouse_id);
                $("#due").val(data.saleinfo.due);
                $("#paid").val((data.saleinfo.grand_total)-(data.saleinfo.paid));
                
            }

            function clearform() {
                $('#ccccc')[0].reset();
               $("#CustomerDuePayModal").modal('hide');
            }

            $("#close").click(function() {
                clearform();
            });


            $('#payment_id').change(function() {
                if ($('#payment_id').val() == 4) {
                    $('#bank_id').empty();
                    $('#bankName').removeClass('d-none');
                    $.ajax({
                        type: "GET",
                        url: "{{ url(Request::segment(1) . '/warehouse-banks-info') }}" + '/' + $(
                            '#warehouse_id').val(),
                        data: {},
                        dataType: "JSON",
                        success: function(data) {
                            if (data) {
                                $('#bank_id').append('<option value="">Select One</option>');
                                $.each(data, function(key, value) {
                                    $('#bank_id').append('<option value="' + value.code +
                                        '">' + value.bank_name + '</option>');

                                });

                            }

                        }
                    });
                } else {
                    $('#bankName').addClass('d-none');
                }

            });

  //Update shift
     $("#CustomerDuePayModal").on('click', '#addBtn', function() {
      
if ($(this).val() == 'Receive') {
    $id = $("#saledueid").val(),
        $.ajax({
            url: "{{ url(Request::segment(1)) }}" + '/customer-due/' + $id,
            method: "PATCH",
            type: "PATCH",
            data: {
                sale_id: $("#saledueid").val(),
                warehouse_id: $("#warehouse_id").val(),
                due: $("#due").val(),
                paid_amount: $("#paid").val(),
                payment_id: $("#payment_id").val(),
                code: $("#bank_id").val(),
                note: $("#note").val(),
            },
            success: function(d) {
                if (d.success) {
                    Swal.fire({
                        icon: 'info',
                        title: " Update Successfully",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $('.data-table').DataTable().ajax.reload();
                    clearform();


                }else{
                    if (d.errors) {
                    //console.log('e');
                    $.each(d.errors, function(key, value) {
                        $('#formerrors').show();
                        $('#formerrors ul').append('<li>' + value +
                            '</li>');
                    });
                    }else{
                        toastr.warning('Data Already Inserted!');
                        //return false
                    }
                }
            },
            error: function(d) {
                // console.log(d);
            }
        });
}
});
//Update  end
        });
    </script>
@endpush
