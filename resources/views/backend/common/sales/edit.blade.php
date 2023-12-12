@extends('backend.layouts.master')
@section('title', 'Sale Update')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css" rel="stylesheet">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sale Update </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Sale</li>
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
                            <h3 class="card-title">Sale </h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.sales.index') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {!! Form::model($sale, [
                                'route' => [Request::segment(1) . '.sales.update', $sale->id],
                                'method' => 'PATCH',
                                'files' => true,
                            ]) !!}
                            <div class="row">
                                @include('backend.common.sales.form')

                                <div class="row">&nbsp;</div>
                                <div class="col-lg-12 col-md-12">
                                    <div id="dynamic" class="row bg-light">
                                        <table class="table table-responsive">
                                            <thead>
                                                <tr>
                                                    {{-- <th style="width: 12%">Barcode </th> --}}
                                                    {{-- <th style="width: 12%">Category <span class="required">*</span></th> --}}
                                                    <th style="width: 24%">
                                                        Product <span class="required">*</span>
                                                        {{-- <button type="button" class="btn btn-primary btn-sm"
                                                            title="Add New Product And Find Product By Type Product Name"
                                                            onclick="showProductForm()">
                                                            <i class="fa fa-plus"></i>
                                                        </button> --}}
                                                    </th>
                                                    <th style="width: 8%">Unit</th>
                                                    <th style="width: 10%">Qty</th>
                                                    <th style="width: 15%">Sale Price</th>
                                                    <th style="width: 10%">Vat(%)</th>
                                                    <th style="width: 10%">Vat Amount</th>
                                                    <th style="width: 15%">Sub Total</th>
                                                    <th style="width: 15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemlist">
                                                @foreach($saleDetails as $key => $saleDetail)
                                                @php $index = $key + 1  @endphp
                                                <tr>
                                                    {{-- <td>
                                                        <div>
                                                            <select class="form-control category_id select2"
                                                                name="category_id[]" required id="category_id_1"
                                                                onchange="getCategoryVal({{ $index }},this);">
                                                                <option value="">Select Category</option>
                                                                @if(count($categories) > 0)
                                                                    @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </td> --}}
                                                    <td width="24%">
                                                        <select class="form-control product_id select2" name="product_id[]"
                                                            id="product_id_{{ $index }}" onchange="getval({{ $index }},this);" required>
                                                            <option value="{{ $saleDetail->product_id }}">
                                                                {{ $saleDetail->product->name }}</option>
                                                        </select>
                                                    </td>
                                                    <td width="12%">
                                                        <div>
                                                            <select class="form-control unit_id select2" name="unit_id[]"
                                                                required id="unit_id_{{ $index }}" onchange="getUnitVal({{ $index }},this);">
                                                                @if(count($units) > 0)
                                                                    @foreach($units as $unit)
                                                                    <option value="{{ $unit->id }}" {{ $unit->id == $saleDetail->unit_id ? 'selected' : ''}}>{{ $unit->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td width="10%">
                                                        <input class="input-sm text-right form-control quantity" type="number"
                                                            name="qty[]" id='qty_id_{{ $index }}'
                                                            placeholder="0.00" data-cell="D{{ $index }}" step="any"
                                                            min="0" max="9999999999999999" required
                                                            data-format="0[.]00" value="{{ $saleDetail->qty }}">
                                                        <span id="show_stock_qty_{{ $index }}"></span>
                                                    </td>
                                                    <td width="15%">
                                                        <input type="number"
                                                            class="input-sm text-right form-control sale_price" placeholder="0.00"
                                                            name="sale_price[]" id='sale_price_id_{{ $index }}' step="any"
                                                            min="0" max="9999999999999999" required
                                                            data-format="0[.]00" data-cell="C{{ $index }}" value="{{ $saleDetail->sale_price }}">
                                                    </td>
                                                    <td width="10%">
                                                        <input type="number" class="form-control input-sm text-right"
                                                            placeholder="0.00" name="product_vat[]" data-cell="V{{ $index }}"
                                                            id='product_vat_id_{{ $index }}' data-format="0[.]00" readonly value="{{ $saleDetail->product_vat }}">
                                                    </td>
                                                    <td width="10%">
                                                        <input class="form-control input-sm text-right" type="number"
                                                            placeholder="0.00" name="product_vat_amount[]" readonly
                                                            data-cell="K{{ $index }}" data-formula="(C{{ $index }}/100*V{{ $index }})*D{{ $index }}"
                                                            id='product_vat_amount_id_{{ $index }}' data-format="0[.]00" value="{{ $saleDetail->product_vat_amount }}">
                                                    </td>
                                                    <td width="15%">
                                                        <input type="text"
                                                            class="amount form-control input-sm text-right" name="total[]"
                                                            placeholder="0.00" data-cell="F{{ $index }}" data-format="0[.]00"
                                                            data-formula="(C{{ $index }}*D{{ $index }})" readonly step="any" min="0"
                                                            max="999999999999999" value="{{ $saleDetail->total }}">
                                                    </td>
                                                    <td width="15%">
                                                        <input type="button" class="btn btn-success addProduct"
                                                            value="+">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                        <div class="row">&nbsp;</div>
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row">
                                                <!-- accepted payments column -->
                                                <div class="col-lg-8 col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="lead">Sale Type:</p>
                                                            <p class="text-muted well well-sm shadow-none"
                                                                style="margin-top: 10px;">
                                                                <select class="form-control select2"
                                                                    name="sale_type_id" id="sale_type_id" readonly
                                                                    required>
                                                                    @if (count($order_types) > 0)
                                                                        @foreach ($order_types as $order_type)
                                                                            <option value="{{ $order_type->id }}" {{ $order_type->id == $sale->payment_type_id ? 'selected':''}}>
                                                                                {{ $order_type->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="lead">Payment Type:</p>
                                                            <p class="text-muted well well-sm shadow-none"
                                                                style="margin-top: 10px;">
                                                                <select class="form-control select2"
                                                                    name="payment_type_id" id="payment_type_id" readonly
                                                                    required>
                                                                    {{-- <option value="">Select</option> --}}
                                                                    @if($sale->order_type_id == '1')
                                                                        @if (count($cash_payment_types) > 0)
                                                                            @foreach ($cash_payment_types as $payment_type)
                                                                                <option value="{{ $payment_type->id }}" {{ $payment_type->id == @$paymentReceipt->payment_type_id ? 'selected':''}}>
                                                                                    {{ $payment_type->name }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    @else
                                                                        @if (count($credit_payment_types) > 0)
                                                                            @foreach ($credit_payment_types as $payment_type)
                                                                                <option value="{{ $payment_type->id }}" {{ $payment_type->id == @$paymentReceipt->payment_type_id ? 'selected':''}}>
                                                                                    {{ $payment_type->name }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    @endif

                                                                </select>
                                                                <br/>
                                                                <span>&nbsp;</span>
                                                                <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank Name" value={{ @$paymentReceipt->bank_name }}>
                                                                <span>&nbsp;</span>
                                                                <input type="text" name="cheque_number" id="cheque_number" class="form-control" placeholder="Cheque Number" value={{ @$paymentReceipt->cheque_number }}>
                                                                <span>&nbsp;</span>
                                                                <input type="text" name="transaction_number" id="transaction_number" class="form-control" placeholder="Transaction Number" value={{ @$paymentReceipt->transaction_number }}>
                                                                <input type="text" name="note" id="note" class="form-control" placeholder="Note">
                                                                <span>&nbsp;</span>
                                                                <input type="text" name="cheque_date" id="cheque_date" class="datepicker form-control" placeholder="Issue Deposit Date" value={{ @$paymentReceipt->cheque_date }}>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-3">
                                                    <p class="lead">Amount Section</p>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tr>
                                                                <th style="width:50%">Subtotal:</th>
                                                                <td>
                                                                    <input type="number" name="sub_total" id="amount"
                                                                        readonly data-cell="G1" data-format="0.00"
                                                                        data-formula="SUM(F1:F5000)" class="form-control"
                                                                        step="any" min="0" max="999999999999" value="{{ $sale->sub_total }}">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Discount Type</th>
                                                                <td>
                                                                    <select class="form-control" name="discount_type"
                                                                        id="discount_type">
                                                                        <option value="Flat" {{ $sale->discount_type == 'Flat' ? 'selected' : '' }}>Flat</option>
                                                                        <option value="Percent" {{ $sale->discount_type == 'Percent' ? 'selected' : '' }}>Percent</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="discount_percent_div">
                                                                <th>Discount Percent:</th>
                                                                <td>
                                                                    <input type="text" name="discount_percent"
                                                                        id="discount_percent" class="form-control"
                                                                        onkeyup="discountCalculation('')" value="{{ $sale->discount_percent }}">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Vat:</th>
                                                                <td>
                                                                    <input type="number" name="total_vat" id="total_vat"
                                                                        readonly data-cell="T1" data-format="0.00"
                                                                        data-formula="SUM(K1:K5000)" class="form-control"
                                                                        step="any" min="0"
                                                                        max="9999999999999999" value="{{ $sale->total_vat }}">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <table class="table table-sticky-bg table-responsive"
                                    style="position: sticky;
                                bottom: 0; z-index: 999;">
                                    <tr>
                                        <th>Total Qty</th>
                                        <td>
                                            <input class="input-sm text-right form-control" type="number"
                                                name="total_quantity" id='total_quantity'
                                                placeholder="0.00" data-cell="" step="any" min="0"
                                                max="99999999999999" required data-format="0[.]00" readonly value="{{ $sale->total_quantity }}">
                                        </td>

                                        <th>Discount:</th>
                                        <td>
                                            <input type="number" name="discount" id="discount_amount"
                                                class="form-control" onkeyup="discountCalculation('')" step="any"
                                                min="0" max="9999999999999999" value="{{ $sale->discount }}">
                                        </td>
                                        <th>Grand Total:</th>
                                        <td>
                                            <input type="number" name="grand_total" id="grand_total"
                                                class="form-control" readonly step="any" min="0"
                                                max="9999999999999999" value="{{ $sale->grand_total }}" />
                                        </td>
                                        <th>Paid:</th>
                                        <td id="PaidAmount">
                                            {!! Form::number('paid', $sale->paid_amount, ['id' => 'paid', 'class' => 'form-control', 'step' => 'any']) !!}
                                        </td>
                                        <th>Due:</th>
                                        <td>
                                            {!! Form::number('due', $sale->due_amount, [
                                                'id' => 'due',
                                                'class' => 'form-control',
                                                'step' => 'any',
                                                'readonly',
                                                'min' => '0',
                                                'max' => '9999999999999999',
                                            ]) !!}
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-success"
                                                id="submitbtn SUBMIT_BTN">Update</button>

                                        </td>
                                    </tr>
                                </table> --}}
                                <table class="table table-sticky-bg table-responsive"
                                    style="position: sticky;
                                bottom: 0; z-index: 999;">
                                    <tr>
                                        <th>Total Qty</th>
                                        <td>
                                            <input class="input-sm text-right form-control" type="number"
                                                name="total_quantity" value="{{ $sale->total_quantity }}" id='total_quantity'
                                                placeholder="0.00" data-cell="" step="any" min="0"
                                                max="99999999999999" required data-format="0[.]00" readonly>
                                        </td>
                                        <th>Discount:</th>
                                        <td>
                                            <input type="number" name="discount" value="{{ $sale->discount }}" id="discount_amount"
                                                class="form-control" onkeyup="discountCalculation('')" step="any"
                                                min="0" max="9999999999999999">
                                        </td>
                                        <th>Grand Total:</th>
                                        <td>
                                            <input type="number" name="grand_total" value="{{ $sale->grand_total }}" id="grand_total"
                                                class="form-control" readonly step="any" min="0"
                                                max="9999999999999999" />
                                        </td>
                                        <th>Available Advance:</th>
                                        <td id="AdvanceAmount">
                                            {!! Form::number('advance_amount', null, ['id' => 'advance_amount', 'class' => 'form-control', 'step' => 'any', 'readonly']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Paid From Advance:</th>
                                        <td id="PaidFromAmount">
                                            {{-- {!! Form::checkbox('paid_from_advance', null, ['id' => 'paid_from_advance', 'class' => 'form-control']) !!} --}}
                                            <input name="paid_from_advance" id="paid_from_advance" type="checkbox" {{ $sale->advance_minus_amount ? 'checked' : '' }}>
                                        </td>
                                        <th>Advance Minus:</th>
                                        <td id="MinusAmount">
                                            {!! Form::number('minus_amount', $sale->advance_minus_amount, ['id' => 'minus_amount', 'class' => 'form-control', 'step' => 'any', 'readonly']) !!}
                                        </td>
                                        <th>Paid:</th>
                                        <td id="PaidAmount">
                                            {!! Form::number('paid', $sale->paid_amount, ['id' => 'paid', 'class' => 'form-control', 'step' => 'any', 'required', 'onkeyup'=>"paidAmount('')"]) !!}
                                        </td>
                                        <th>Due:</th>
                                        <td>
                                            {!! Form::number('due', $sale->due_amount, [
                                                'id' => 'due',
                                                'class' => 'form-control',
                                                'step' => 'any',
                                                'readonly',
                                                'min' => '0',
                                                'max' => '9999999999999999',
                                                'required'
                                            ]) !!}
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-success"
                                                id="submitbtn SUBMIT_BTN">Submit</button>

                                        </td>
                                    </tr>
                                </table>

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

    <script>
        function showCustomerForm() {

            var page = "{{ url(Request::segment(1) . '/customers/create') }}";
            var myWindow = window.open(page, "_blank", "scrollbars=yes,width=700,height=1000,top=30");
            // focus on the popup //
            myWindow.focus();
        }

        // onkeyup
        function paidAmount(){
            var total = $('#grand_total').val();
            var minus_amount = $('#minus_amount').val();
            if(minus_amount == ''){
                var paid_amount = $('#paid').val();
            }else{
                var paid_amount = parseFloat(minus_amount) + parseFloat($('#paid').val());
            }

            var due = total - paid_amount;
            $('#due').val(due);
        }

        function customerAdvanceBalanceInfo(customer_id){
            if (customer_id) {
                $.ajax({
                    type: "GET",
                    url: "{{ url(Request::segment(1) . '/customer-advance-balance-info') }}" + '/' +
                    customer_id,
                    dataType: "JSON",
                    success: function(data) {
                        console.log('data',data);
                        $('#advance_amount').val(data)
                    }
                });
            }
        }


        $(document).ready(function() {

            $('#discount_percent').prop("readonly", true);
            $('#discount_amount').prop("readonly", false);

            //$('.lc_div').hide();
            $('.select2').select2();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var customer_id = $('#customer_id').val();
            customerAdvanceBalanceInfo(customer_id);

            $('#customer_id').change(function() {
                var customer_id = $(this).val();
                if (customer_id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url(Request::segment(1) . '/customer-advance-balance-info') }}" + '/' +
                        customer_id,
                        dataType: "JSON",
                        success: function(data) {
                            console.log('data',data);
                            $('#advance_amount').val(data)
                        }
                    });
                }
            });

            $('#paid_from_advance').change(function(){
                if ($("#paid_from_advance").is(':checked')) {
                    var grand_total = $('#grand_total').val();
                    var advance_amount = $('#advance_amount').val();
                    console.log('grand_total',typeof grand_total)
                    console.log('advance_amount',typeof advance_amount)
                    if(grand_total < advance_amount ){
                        console.log('1 =',grand_total)
                        $('#minus_amount').val(advance_amount)
                    }else{
                        console.log('2 =',advance_amount)
                        $('#minus_amount').val(grand_total)
                    }
                    var minus_amount = $('#minus_amount').val();
                    // $('#paid').val(grand_total - minus_amount)
                    $('#paid').val('');
                    $('#due').val(grand_total - minus_amount)
                }else{
                    var minus_amount = $('#minus_amount').val(0);
                }
            })

            //product fetch for sale
            $('#product_id_1').select2({
                placeholder: 'Type Product Name',
                minimumInputLength: 1,
                ajax: {
                    type: "POST",
                    url: "{{ url(Request::segment(1) . '/find-product-info') }}",
                    dataType: "JSON",
                    delay: 250,
                    data: function(params) {
                        console.log('params', params)
                        return {
                            q: params.term,
                            store_id: $('#store_id').val(),
                        };
                    },
                    //mark:select
                    processResults: function(data) {
                        console.log(data)
                        return {
                            results: $.map(data, function(item) {
                                // console.log('item', item)
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };

                    },
                    cache: true
                }
            });


            $(document).on('click', '.addProduct', function(event) {
                // $('.addProduct').on('click', function(event) {
                var product = $('.product_id').html();
                var n = ($('#itemlist tr').length - 0) + 1;
                var tr =
                    '<tr>' +
                    // '<td width="12%"><div><select  class="form-control category_id select2" name="category_id[]" id="category_id_' +
                    // n + '" onchange="getCategoryVal(' + n + ',this);" required>' + category +
                    // '</select></div></td>' +
                    '<td><select class="form-control product_id select2" name="product_id[]" id="product_id_' +
                    n + '" onchange="getval(' + n +
                    ',this);" required ></select></td>' +

                    '<td width="12%"><div><select class="form-control unit_id select2" name="unit_id[]" id="unit_id_' +
                    n + '" onchange="getUnitVal(' + n +
                    ',this);" required > required' +
                    '</select></div></td>' +

                    '<td width="12%"><input type="number" class="input-sm text-right form-control quantity" name="qty[]" id="qty_id_' +
                    n + '" required  step="any" placeholder="0.00" data-cell="D' + n +
                    '" step="any" min="0" max="9999999999999999" data-format="0[.]00"><span id="show_stock_qty_' + n + '"></span></td>' +

                    '<td width="12%"><input type="number" step="any" min="0" max="9999999999999999" class="input-sm text-right form-control sale_price"  data-format="0[.]00" name="sale_price[]" id="sale_price_id_' +
                    n + '" data-cell="c' + n + '"   value="" required></td>' +

                    '<td width="12%"><input type="number" class="form-control input-sm text-right" placeholder="0.00" data-format="0[.]00" name="product_vat[]" id="product_vat_id_' +
                    n + '"  data-cell="V' + n + '" required readonly></td>' +

                    '<td><input type="number" class="form-control input-sm text-right" placeholder="0.00" name="product_vat_amount[]" id="product_vat_amount_id_' +
                    n + '" readonly data-cell="K' + n + '" data-formula="(C' + n + '/100*V' + n + ')*D' +
                    n + ' " data-format="0[.]00" required></td>' +
                    '<td style="widht:12px"><input class="form-control input-sm text-right" placeholder="0.00" readonly name="total[]"  data-cell="F' +
                    n + '" data-format="0[.]00" data-formula="(C' + n + '*D' + n + ') "></td>' +
                    '<td><span class="d-inline-flex"><input type="button" class="btn btn-success addProduct" value="+" title="Add New"> <input type="button" class="btn btn-danger delete float-left" style="margin-left: 5px" value="x" title="Remove This Product"></span></td>' +
                    '</tr>';

                $('#itemlist').append(tr);
                $form = $('#dynamic').calx();
                $form.calx('update');
                $form.calx('getCell', 'G1').setFormula('SUM(F1:F' + 5000 + ')');
                $form.calx('getCell', 'G1').calculate();

                // search product start
                $('#product_id_' + n).select2({
                    placeholder: 'Type Product Name',
                    minimumInputLength: 1,
                    ajax: {
                        type: "POST",
                        url: "{{ url(Request::segment(1) . '/find-product-info') }}",
                        dataType: "JSON",
                        delay: 250,
                        data: function(params) {
                            // console.log('params', params)
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            //console.log('data2', data)
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.name,
                                        id: item.id
                                    }
                                })
                            };

                        },
                        cache: true
                    }
                });
                $('#product_id_' + n).select2('open').trigger('select2:open');
                // search product end
            });

            //new item
            $('#itemlist').delegate('.delete', 'click', function() {
                $(this).parent().parent().parent().remove();
                $form = $('#dynamic').calx();
                $form.calx('update');
                $form.calx('getCell', 'G1').setFormula('SUM(F1:F' + 5000 + ')');
                $form.calx('getCell', 'G1').calculate();
                $form.calx('getCell', 'T1').setFormula('SUM(K1:K' + 5000 + ')');
                $form.calx('getCell', 'T1').calculate();

                var total_vat = $("#total_vat").val();
                var sub_total = $("#amount").val();
                var grand_total = parseFloat(sub_total) + parseFloat(total_vat);
                $('#grand_total').val(grand_total);
            });

            // real time change
            $('#itemlist').delegate('.quantity, .sale_price', 'keyup', function () {
                $form = $('#dynamic').calx();
                $form.calx('update');
                $form.calx('getCell', 'G1').setFormula('SUM(F1:F' + 5000 + ')');
                $form.calx('getCell', 'G1').calculate();
                $form.calx('getCell', 'T1').setFormula('SUM(K1:K' + 5000 + ')');
                $form.calx('getCell', 'T1').calculate();
                var sub_total = $("#amount").val();
                var total_vat = $("#total_vat").val();
                var discount_amount = $("#discount_amount").val();

                if(discount_amount == ''){
                    var grand_total = parseFloat(sub_total) + parseFloat(total_vat);
                }else{
                    var grand_total = (parseFloat(sub_total) + parseFloat(total_vat)) - parseFloat(discount_amount);
                }
                $('#grand_total').val(grand_total);

                quantitySum();
            });

        });

        function getval(row, sel) {
            // alert(sel.value);
            var current_row = row;
            var store_id = $('#store_id').val();
            var current_product_id = sel.value;
            if (store_id === '') {
                $('#product_id_' + current_row).val('');
                alert('You selected store first!');
                return false;
            }

            if (current_row > 1) {
                for (let index = 1; index < current_row; index++) {
                    var previous_product_id = $(('#product_id_' + index)).val();
                    var current_product_id = $('#product_id_' + current_row).val();
                    if (previous_product_id === current_product_id) {
                        $('#product_id_' + current_row).val('');
                        alert('You selected same product, Please selected another product!');
                        return false;
                    }
                }
            }

            $.ajax({
                url: "{{ URL(Request::segment(1) . '/sale-relation-data') }}",
                method: "get",
                data: {
                    current_product_id: current_product_id,
                    store_id: store_id
                },

                success: function(res) {
                    console.log('res',res)
                    if(res.data.sale_price == ''){
                        $('#product_id_' + current_row).val('');
                        alert('You selected product minimum one time stock in, Please selected another product or stock in first!');
                        return false;
                    }
                    $(("#unit_id_" + current_row)).html(res.data.unitOptions);
                    $(("#sale_price_id_" + current_row)).val(res.data.sale_price);
                    $('#show_stock_qty_' + current_row).html(res.data.current_stock);
                    $('#product_vat_id_' + current_row).val(res.data.vat_percent);
                },
                error: function(err) {
                    console.log(err)
                }
            })

            //focus
        }

        function quantitySum(){
            console.log('quantitySum')
            var t = parseInt(0);
            $('.quantity').each(function(i,e){
                var amt = $(this).val();
                t += parseInt(amt);
            });
            $('#total_quantity').val(t);
        }

        $('#discount_type').on('change', function(event) {
            var discount_type = $('#discount_type').val();
            var total_vat = $("#total_vat").val();
            var sub_total = $("#amount").val();

            if (discount_type === 'Flat') {
                $('#discount_percent').prop("readonly", true); // Element(s) are now enabled.
                $('#discount_amount').prop("readonly", false);
                $('#discount_percent').val('');
                $('#discount_amount').val('');
                $('#grand_total').val(grand_total);
            } else {
                $('#discount_percent').prop("readonly", false); // Element(s) are now enabled.
                $('#discount_amount').prop("readonly", true);
                $('#discount_amount').val('');
                $('#grand_total').val(grand_total);
            }

            var discount_amount = parseFloat($('#discount_amount').val());
            var grand_total = parseFloat(sub_total) + parseFloat(total_vat);
            if (sub_total > discount_amount) {
                alert('You Can Not Discount More than Subtotal Price !');
                return false;
            }
        })

        function discountCalculation() {
            $("#paid").val('');
            $("#due").val('');
            var discount_type = $('#discount_type').val();
            var sub_total = $("#amount").val();
            var total_vat = $("#total_vat").val();
            var grand_total = parseFloat(sub_total) + parseFloat(total_vat);

            if (discount_type == 'Flat') {
                var discount_amount = $('#discount_amount').val();
                if (discount_amount !== '') {
                    discount_amount = parseFloat(discount_amount);
                    var discount = grand_total - discount_amount;
                    var final_amount = discount;
                } else {
                    var final_amount = grand_total;
                }
                $('#discount_percent').val('');
            } else {
                var discount_percent = $('#discount_percent').val();
                if (discount_percent !== '') {
                    discount_percent = parseFloat(discount_percent);
                    var discount = (grand_total * discount_percent) / 100;
                    var final_amount = grand_total - discount;
                } else {
                    var final_amount = grand_total;
                }
                $('#discount_amount').val(discount);
            }

            $('#total_amount').val(grand_total);
            $('#grand_total').val(final_amount);
            if (sub_total < discount_amount) {
                alert('You Can Not Discount More than Subtotal Price !');
                $('#discount_amount').val(sub_total);
                // discountCalculation();
            }


            if ($("#paid_from_advance").is(':checked')) {
                var grand_total = $('#grand_total').val();
                var advance_amount = $('#advance_amount').val();
                console.log('grand_total',typeof grand_total)
                console.log('advance_amount',typeof advance_amount)
                if(grand_total < advance_amount ){
                    console.log('1 =',grand_total)
                    $('#minus_amount').val(advance_amount)
                }else{
                    console.log('2 =',advance_amount)
                    $('#minus_amount').val(grand_total)
                }
                var minus_amount = $('#minus_amount').val();
            }else{
                var minus_amount = $('#minus_amount').val(0);

                var sale_type_id = $('#sale_type_id').val();
                if (sale_type_id === '2') {
                    console.log(2)
                    $("#paid").val(0);
                    $('#due').val(final_amount);
                    $('#paid').prop("readonly", true);
                } else {
                    console.log(1)
                    $('#paid').prop("readonly", false);
                    $("#paid").val('');
                    $('#due').val('');
                    $('#bank_name').hide();
                    $('#cheque_number').hide();
                    $('#cheque_date').hide();
                }
            }

        }

        $('#sale_type_id').change(function() {
            $("#paid").val();
            $("#due").val();
            $('#transaction_number').hide();
            $('#bank_name').hide();
            $('#cheque_number').hide();
            $('#cheque_date').hide();
            $('#note').hide();
            discountCalculation();
            var sale_type_id = $('#sale_type_id').val();
            $.ajax({
                type: "GET",
                url: "{{ url(Request::segment(1) . '/get-payment-list') }}",
                data: {
                    sale_type_id:sale_type_id
                },
                success: function(res) {
                    console.log('data',res.data);
                    $('#payment_type_id').html(res.data.paymentTypeOptions);
                }
            });
        });

        $('#payment_type_id').change(function() {
            // discountCalculation();
            if($('#payment_type_id').val() == '3'){
                $('#bank_name').show();
                $('#cheque_number').show();
                $('#cheque_date').show();
            }else{
                $('#bank_name').hide();
                $('#cheque_number').hide();
                $('#cheque_date').hide();
            }
        });

        //automatically call after two seconds/
        //tab index changing
        $('#date').on("keydown", function() {
            $('#product_id_1').select2('open').trigger('select2:open');
        });


        // function showProductForm() {
        //     var page = "{{ url(Request::segment(1) . '/stock-transfer-warehouse-to-van') }}";
        //     var myWindow = window.open(page, "_blank", "scrollbars=yes,width=700,height=1000,top=30");
        //     // focus on the popup //
        //     myWindow.focus();
        // }

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
