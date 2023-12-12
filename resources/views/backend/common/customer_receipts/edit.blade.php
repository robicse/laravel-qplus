@extends('backend.layouts.master')
@section('title', 'Customer Receive')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer Receive</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Customer Receive</li>
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
                            <h3 class="card-title">Customer Receive</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.customer-receipts.index') }}">
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
                            {{-- {!! Form::open(['route' => Request::segment(1) . '.customer-receipts.store', 'class' => 'form', 'id' => 'form']) !!} --}}
                            {!! Form::model($paymentReceipt, [
                                'route' => [Request::segment(1) . '.customer-receipts.update', $paymentReceipt->id],
                                'method' => 'PATCH',
                                'files' => true,
                            ]) !!}
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Date<span class="required"> *</span></label>
                                        {!! Form::date('date', null, ['id' => 'date', 'class' => 'form-control mb-1', 'required']) !!}

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
                                        <label>Select Customer:<span class="required"> *</span></label>
                                        <select class="form-control select2" name="customer_id" id="customer_id" required>
                                            <option>Select One</option>
                                            {{-- @if (@Auth::user()->store_id) --}}
                                                @if (count($customers))
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" {{ $customer->id == $paymentReceipt->customer_id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                    @endforeach
                                                @endif
                                            {{-- @endif --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Total Due</label>
                                        {!! Form::number('due', null, ['id' => 'due', 'class' => 'form-control mb-1', 'readonly']) !!}

                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Received Now<span class="required"> *</span></label>
                                        {!! Form::number('amount', null, ['id' => 'amount', 'class' => 'form-control mb-1', 'required']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="customer">Select Payment<span class="required"> *</span></label>
                                {!! Form::select('payment_type_id', $paymentTypes, null, [
                                    'id' => 'payment_type_id',
                                    'class' => 'form-control select2',
                                    'required',
                                    'placeholder' => 'Select One',
                                ]) !!}
                            </div>
                            <span>&nbsp;</span>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank Name">
                            <span>&nbsp;</span>
                            <input type="text" name="cheque_number" id="cheque_number" class="form-control" placeholder="Cheque Number">
                            <span>&nbsp;</span>
                            <input type="text" name="transaction_number" id="transaction_number" class="form-control" placeholder="Transaction Number">
                            <input type="text" name="note" id="note" class="form-control" placeholder="Note">
                            <span>&nbsp;</span>
                            <input type="text" name="cheque_date" id="cheque_date" class="datepicker form-control" placeholder="Issue Deposit Date ">
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
            $('#store_id').change(function() {
                var store_id = $(this).val();
                $('#itemlist tr').not(":last").remove();
                $('#total_due').val("");
                $.ajax({
                    url: "{{ url(Request::segment(1)) }}" + '/get-store-customer',
                    method: 'POST',
                    data: {
                        store_id: store_id
                    },
                    success: function(res) {
                        console.log(res);
                        if (res !== '') {
                            $html = '<option value="">Select One</option>';
                            res.forEach(element => {
                                $html += '<option value="' + element.id + '">' + element
                                    .name + '</option>';
                            });
                            $('#customer_id').html($html);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                })
            })
            $('#customer_id').change(function() {
                console.log('a')
                var store_id = $(this).val();
                var customer_id = $(this).val();
                console.log('customer_id',customer_id)
                if (customer_id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url(Request::segment(1) . '/customer-due-amount') }}" + '/' +
                        customer_id,
                        dataType: "JSON",
                        success: function(data) {
                            console.log('data',data);
                            $('#amount').val("");
                            $('#total_due').val(data);
                            if($('#total_due').val() == 0){
                                $('#SUBMIT_BTN').prop('disabled',true);
                            }else{
                                $('#SUBMIT_BTN').prop('disabled',false);
                            }
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
                }else if($('#payment_type_id').val() == '2' || $('#payment_type_id').val() == '5') {
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
