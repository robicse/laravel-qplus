@extends('backend.layouts.master')
@section('title', 'Supplier Receive')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Supplier Receive</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Supplier Receive</li>
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
                            <h3 class="card-title">Supplier Receive</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.supplier-payments.index') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="dynamic">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {!! Form::open(['route' => Request::segment(1) . '.supplier-payments.store', 'class' => 'form', 'id' => 'form']) !!}
                            <div class="row">
                                {{-- <div class="col-lg-4 d-none">
                                    <div class="form-group">
                                        <label for="voucher_no">Voucher No<span class="required"> *</span></label>
                                        {!! Form::text('voucher_no', $voucherCode, [
                                            'id' => 'voucher_no',
                                            'class' => 'form-control',
                                            'required',
                                            'readonly',
                                            'autofocus'
                                        ]) !!}

                                    </div>
                                </div> --}}
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Date<span class="required"> *</span></label>
                                        {!! Form::date('date', date('Y-m-d'), ['id' => 'date', 'class' => 'form-control mb-1', 'required']) !!}

                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="store_id">Select Store<span class="required"> *</span></label>
                                        {!! Form::select('store_id', $stores, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => 'Select One',
                                            'id' => 'store_id',
                                            'required',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Select Supplier:<span class="required"> *</span></label>
                                        <select class="form-control select2" name="supplier_id" id="supplier_id" required>
                                            <option>Select One</option>
                                            {{-- @if (@Auth::user()->store_id) --}}
                                                @if (count($suppliers))
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                @endif
                                            {{-- @endif --}}
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row bg-light">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>SL <span class="required">*</span></th>
                                                    <th>Invoice No <span class="required">*</span></th>
                                                    <th>Check</th>
                                                    <th>Due Amount</th>
                                                    <th>Paid Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemlist">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>
                                                        <input type="text" id="total_due" class="form-control" disabled
                                                            data-formula='SUM(D1:D5000)' data-cell='K1' step='any'
                                                            placeholder='0.00'>
                                                    </th>

                                                    <th>
                                                        <div class="form-group">
                                                            {!! Form::number('amount', null, [
                                                                'id' => 'amount',
                                                                'class' => 'form-control',
                                                                'required',
                                                                'step' => 'any',
                                                                'data-cell' => 'G1',
                                                                'data-format' => '0[.]00',
                                                                'placeholder' => '0.00',
                                                                'min' => 0,
                                                                'max' => 9999999999999999,
                                                                'data-formula' => 'SUM(F1:F5000)',
                                                                'readonly',
                                                                'required',
                                                            ]) !!}
                                                        </div>
                                                    </th>
                                                </tr>


                                            </tbody>
                                            <tfoot>
                                            </tfoot>

                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="supplier">Select Payment<span class="required"> *</span></label>
                                {!! Form::select('payment_type_id', $paymentTypes, null, [
                                    'id' => 'payment_type_id',
                                    'class' => 'form-control select2',
                                    'required',
                                    'placeholder' => 'Select One',
                                ]) !!}
                                <span>&nbsp;</span>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank Name">
                                <span>&nbsp;</span>
                                <input type="text" name="cheque_number" id="cheque_number" class="form-control" placeholder="Cheque Number">
                                <span>&nbsp;</span>
                                <input type="text" name="transaction_number" id="transaction_number" class="form-control" placeholder="Transaction Number">
                                <input type="text" name="note" id="note" class="form-control" placeholder="Note">
                                <span>&nbsp;</span>
                                <input type="text" name="cheque_date" id="cheque_date" class="datepicker form-control" placeholder="Issue Deposit Date ">
                            </div>
                            {{-- <div class="form-group d-none" id="bankName">
                                <label for="bank_id">Bank Name <span class="required">*</span></label>
                                <select class="form-control select2" name="code" id="bank_id" style="width: 100%">
                                    <option value="">Select One *</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="note">Note<span class="required"> </span></label>
                                {!! Form::text('note', null, ['id' => 'note', 'class' => 'form-control']) !!}

                            </div> --}}
                            <div class="card-footer">
                                <button type="submit" id="SUBMIT_BTN" class="btn btn-primary">Submit</button>
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
@stop
@section('calx')
    <script src="{{ asset('backend/jquery-calx-sample-2.2.8.min.js') }}"></script>
@endsection
@push('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"
    integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
        $('.select2').select2();
        $(document).ready(function() {
            // $('#store_id').change(function() {
            //     var store_id = $(this).val();
            //     $('#itemlist tr').not(":last").remove();
            //     $('#total_due').val("");
            //     $.ajax({
            //         url: "{{ url(Request::segment(1)) }}" + '/get-store-supplier',
            //         method: 'POST',
            //         data: {
            //             store_id: store_id
            //         },
            //         success: function(res) {
            //             console.log(res);
            //             if (res !== '') {
            //                 $html = '<option value="">Select One</option>';
            //                 res.forEach(element => {
            //                     $html += '<option value="' + element.id + '">' + element
            //                         .name + '</option>';
            //                 });
            //                 $('#supplier_id').html($html);
            //             }
            //         },
            //         error: function(err) {
            //             console.log(err);
            //         }
            //     })
            // })
            $('#supplier_id').change(function() {
                console.log('a')
                var supplier_id = $(this).val();
                console.log('supplier_id',supplier_id)
                if (supplier_id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url(Request::segment(1) . '/supplier-due-balance-info') }}" + '/' +
                        supplier_id,
                        dataType: "JSON",
                        success: function(data) {
                            console.log('data',data);
                            //$('#itemlist tr').remove();
                            $('#itemlist tr').not(":last").remove();
                            $('#amount').val("");
                            $.each(data, function(index, value) {
                                //console.log(value);
                                var n = ($('#itemlist tr').length - 0) + 1;
                                var tr =
                                    '<tr><td><input type="text" class="input-sm text-right form-control" name="payment_receipt_id[]" id="payment_receipt_id_' +
                                    n + '" required readonly step="any" value="' + value
                                    .payment_receipt_id + '"></td>' +

                                    '<td ><input type="text" class="input-sm text-right form-control" name="invoice_no[]" id="invoice_no_id_' +
                                    n + '" required readonly step="any" value="' + value
                                    .order_id + '"></td>' +

                                    '<td ><input type="checkbox" class="input-sm text-right form-control" name="check_amount[]" id="check_amount_id_' +
                                    n + '" onclick="getCheckUncheck(' + n +
                                    ',this);"></td>' +

                                    '<td ><input type="number" class="input-sm text-right form-control" name="due_amount[]" step="any" readonly data-format="0[.]00"   data-cell="D' +
                                    n + '" id="due_amount_id_' +
                                    n + '" value="' + value.due_amount + '"></td>' +


                                    '<td ><input type="number" class="form-control input-sm text-right" placeholder="0.00"  data-cell="F' +
                                    n +
                                    '" step="any" data-format="0[.]00" min="0" "max"="9999999999999999" name="paid_amount[]" id="paid_amount_id_' +
                                    n + '" value="" onkeyup="getUpdatePaidAmount(' + n +
                                    ',this);"></td>' + '</tr>';
                                $('#itemlist').prepend(tr);
                            });
                            $('#total_due').val("");
                            $form = $('#dynamic').calx();
                            $form.calx('update');
                            $form.calx('getCell', 'K1').setFormula('SUM(D1:D' + 5000 + ')');
                            $form.calx('getCell', 'K1').calculate();
                        }
                    });

                } else {
                    $('#itemlist tr').not(":last").remove();
                    $('#amount').val("");
                    $('#total_due').val("");
                }
            });

             $('#payment_type_id').change(function() {
                console.log('2')
            });
        });

        function getCheckUncheck(row, sel) {
            var current_row = row;
            //alert(current_row);
            //var check_amount_id = $('#check_amount_id_' + current_row).val();
            if ($("#check_amount_id_" + current_row).is(':checked')) {
                //console.log("checked");
                var due_amount = $('#due_amount_id_' + current_row).val();
                $('#paid_amount_id_' + current_row).val(due_amount);

                $form = $('#dynamic').calx();
                $form.calx('update');
                $form.calx('getCell', 'G1').setFormula('SUM(F1:F' + 5000 + ')');
                $form.calx('getCell', 'G1').calculate();
            } else {
                //console.log("unchecked");
                $('#paid_amount_id_' + current_row).val("");
                $form = $('#dynamic').calx();
                $form.calx('update');
                $form.calx('getCell', 'G1').setFormula('SUM(F1:F' + 5000 + ')');
                $form.calx('getCell', 'G1').calculate();
            }
        }

        function getUpdatePaidAmount(row, sel) {
            var current_row = row;
            var due_amount = parseFloat($('#due_amount_id_' + current_row).val());
            var paid_amount = parseFloat($('#paid_amount_id_' + current_row).val());
            console.log(paid_amount);
            if (due_amount < paid_amount) {
                $('#paid_amount_id_' + current_row).val("");
            }
            $form = $('#dynamic').calx();
            $form.calx('update');
            $form.calx('getCell', 'G1').setFormula('SUM(F1:F' + 5000 + ')');
            $form.calx('getCell', 'G1').calculate();
        }

        $(function() {
            $('#note').hide();
            $('#transaction_number').hide();
            $('#bank_name').hide();
            $('#cheque_number').hide();
            $('#cheque_date').hide();
            $('#payment_type_id').change(function(){
                if($('#payment_type_id').val() == '3') {
                    $('#bank_name').show();
                    $('#cheque_number').show();
                    $('#cheque_date').show();
                    $('#transaction_number').hide();
                    $('#note').hide();
                }else if($('#payment_type_id').val() == '2') {
                    $('#transaction_number').show();
                    $('#bank_name').hide();
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                    $('#note').hide();
                }else if($('#payment_type_id').val() == '4') {
                    $('#note').show();
                    $('#bank_name').hide();
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                    $('#transaction_number').hide();
                } else {
                    $('#note').val('');
                    $('#note').hide();
                    $('#transaction_number').hide();
                    $('#bank_name').val('');
                    $('#bank_name').hide();
                    $('#cheque_number').val('');
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                }
            });
        });
    </script>
@endpush
