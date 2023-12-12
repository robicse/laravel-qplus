@extends('backend.layouts.master')
@section('title', 'Sale Create')
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"
        integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sale</h1>
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
                            @can('sales-create')
                            <h3 class="card-title">Sale Create</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.sales.index') }}">
                                    <button class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>
                                        Back
                                    </button>
                                </a>
                            </div>
                            @endcan
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
                            @php
                            $sale = '';
                            @endphp
                            {!! Form::open(['route' => Request::segment(1) . '.sales.store', 'method' => 'POST', 'files' => true]) !!}
                            @include('backend.common.sales.form')
                            <div class="col-lg-12 col-md-12 ">
                                <div id="dynamic" class="row card-info  card border  customcontent" >
                                    <table class="table table-responsive" id="table1">
                                        <thead>
                                            <tr>
                                                <th>Category <span class="required">*</span></th>
                                                <th>
                                                    Product <span class="required">*</span>
                                                </th>
                                                <th>Available Stock <span class="required">*</span></th>
                                                <th>Quantity <span class="required">*</span></th>
                                                <th>Unit <span class="required">*</span></th>
                                                <th>Amount (Unit) <span class="required">*</span></th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemlist">
                                            <tr>
                                                <td>
                                                    <div>
                                                        <select class="form-control category_id select2"
                                                            name="category_id[]" required id="category_id_1"
                                                            onchange="getCategoryVal(1,this);">
                                                            <option value="">Select Category</option>
                                                            @if(count($categories) > 0)
                                                                @foreach($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control product_id select2"
                                                        name="product_id[]" id="product_id_1"
                                                        required onchange="getProductVal(1,this);">
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="input-sm text-right form-control" type="number"name="available_stock_qty[]" id='available_stock_qty_1'>
                                                </td>
                                                <td>
                                                    <input class="input-sm text-right form-control quantity" type="number" onkeyup="quantitySum()"
                                                        name="quantity[]" id='quantity_id_1'
                                                        placeholder="0.00" data-cell="D1" step="any" min="0"
                                                        max="99999999999999" required data-format="0[.]00">
                                                </td>
                                                <td>
                                                    <div>
                                                        <select class="form-control unit_id select2"
                                                            name="unit_id[]" required id="unit_id_1">
                                                            <option value="">Select Unit</option>
                                                            @if(count($units) > 0)
                                                                @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number"  step="any"
                                                        class="input-sm text-right amount
                                                    form-control"
                                                        placeholder="0.00" name="amount[]" onkeyup="amountSum()"
                                                        id='amount_id_1' required data-format="0[.]00"
                                                        data-cell="C1" step="any" min="0"
                                                        max="99999999999999">
                                                </td>
                                                <td>
                                                    <input type="button" class="btn btn-success addProduct"
                                                        value="+">
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    Total Quantity: <span class="required">*</span>
                                                    <input class="input-sm text-right form-control" type="number"
                                                        name="total_quantity" id='total_quantity'
                                                        placeholder="0.00" data-cell="" step="any" min="0"
                                                        max="99999999999999" required data-format="0[.]00" readonly>
                                                </td>
                                                <td>Payable Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="payable_amount" id="payable_amount" placeholder="0.00" data-cell=""
                                                    data-format="0[.]00" data-formula=""
                                                    step="any" min="0" max="99999999999999" readonly>
                                                </td>
                                                <td>Discount Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"  onkeyup="discountAmount()"
                                                    name="discount_amount" id="discount_amount" placeholder="0.00"
                                                    step="any" min="0" max="99999999999999">
                                                </td>
                                                <td>Total Sale Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="grand_total" id="grand_total" placeholder="0.00" data-cell=""
                                                    data-format="0[.]00" data-formula=""
                                                    step="any" min="0" max="99999999999999" readonly>
                                                </td>
                                                <td>Paid Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="paid_amount" id="paid_amount" placeholder="0.00"
                                                    step="any" min="0" max="99999999999999">
                                                </td>
                                                <td>Due Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="due_amount" id="due_amount" placeholder="0.00"
                                                    step="any" min="0" max="99999999999999" readonly>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            {!! Form::close() !!}
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
        $(document).ready(function() {
            $('.select2').select2();
            $(document).on('click', '.addProduct', function() {
                var category = $('.category_id').html();
                var unit = $('.unit_id').html();
                var n = ($('#itemlist tr').length - 0) + 1;
                var tr =
                    '<tr>' +
                        '<td width="12%"><div><select  class="form-control category_id select2" name="category_id[]" id="category_id_' +
                    n + '" onchange="getCategoryVal(' + n + ',this);" required>' + category +
                    '</select></div></td>' +
                    '<td width="12%"><select class="form-control product_id select2"  name="product_id[]" id="product_id_' +
                    n + '" onchange="getProductVal(' + n + ',this);" required></select> </td>' +
                    '<td width="12%"><input type="number"  class="input-sm text-right form-control" name="available_stock_qty[]" id="available_stock_qty_' +
                    n + '"></td>' +
                    '<td width="12%"><input type="number"  class="input-sm text-right form-control quantity" onkeyup="quantitySum()" name="quantity[]" id="quantity_id_' +
                    n +
                    '" required   step="any" min="0" max="99999999999999" placeholder="0.00" data-cell="" data-format="" data-format="0[.]00"><span id="available_stock_qty_' + n + '"></span></td>' +
                    '<td width="12%"><div><select  class="form-control unit_id select2" name="unit_id[]" id="unit_id_' +
                    n + '" required>' + unit +
                    '</select></div></td>' +
                    '<td width="12%"><input type="number" class="input-sm text-right form-control amount" onkeyup="amountSum()"  data-format="0[.]00" name="amount[]" id="amount_id_' +
                    n + '" data-cell=""   value="" required  step="any" min="0" max="99999999999999"></td>' +
                    '<td><span class="d-inline-flex"><input type="button"  class="btn btn-success addProduct" value="+"> <input type="button" class="btn btn-danger delete float-left" style="margin-left: 5px" value="x" title="Remove This Product"></span></td>' +
                    '</tr>';
                $('#itemlist').append(tr);
                $('.select2').select2();
                $('#category_id_' + n).select2('open').trigger('select2:open');
            });

            //new item
            $('#itemlist').delegate('.delete', 'click', function() {
                $(this).parent().parent().parent().remove();
            });
        });

        function getCategoryVal(row, sel) {
            console.log('getCategoryVal')
            var current_row = row;
            var current_category_id = sel.value;
            // console.log('current_category_id',current_category_id)

            if (current_row > 1) {
                for (let index = 1; index < current_row; index++) {
                    var previous_category_id = $(('#category_id_' + index)).val();
                    console.log('previous_category_id',previous_category_id)
                    var current_category_id = $('#category_id_' + current_row).val();
                    if (previous_category_id === current_category_id) {
                        $('#category_id_' + current_row).val('');
                        alert('You selected same category, Please selected another category!');
                        return false;
                    }
                }
            }

            $.ajax({
                url: "{{ URL(Request::segment(1) . '/category-product-info') }}",
                method: "get",
                data: {
                    current_category_id: current_category_id
                },
                success: function(res) {
                    // console.log('res', res)
                    $(("#product_id_" + current_row)).html(res.data.productOptions);
                },
                error: function(err) {
                    console.log(err)
                }
            })
        }

        function getProductVal(row, sel) {
            var store_id = $('#store_id').val();
            if(store_id){
                var current_row = row;
                var current_product_id = sel.value;
                if(current_row > 1){
                    var previous_row = current_row - 1;
                    var previous_product_id = $('#product_id_'+previous_row).val();
                    if(previous_product_id === current_product_id){
                        $('#product_id_'+current_row).val('');
                        alert('You selected same product, Please selected another product!');
                        return false
                    }
                }

                // check product services
                var all_product_ids = [];
                $(".product_id").each(function(i,e) {
                    all_product_ids[i] = this.value;
                });

                $.ajax({
                    url : "{{URL(Request::segment(1) . '/sale-relation-data')}}",
                    method : "get",
                    data : {
                        store_id : store_id,
                        current_product_id : current_product_id,
                        all_product_ids : all_product_ids
                    },
                    success : function (res){
                        // console.log(res.data)
                        $("#unit_id_"+current_row).html(res.data.unitOptions);
                        $("#available_stock_qty_"+current_row).val(res.data.current_stock);
                        $("#amount_id_"+current_row).val(res.data.sale_price);
                    },
                    error : function (err){
                        console.log(err)
                    }
                })
            }else{
                alert('Please select first store!');
                location.reload();
            }
        }

        function quantitySum(){
            console.log('quantitySum')
            var t = parseInt(0);
            $('.quantity').each(function(i,e){
                var amt = $(this).val();
                t += parseInt(amt);
            });
            $('#total_quantity').val(t);
            amountSum();
        }

        function amountSum(){
            console.log('amountSum')
            var t = parseFloat(0);
            $('.amount').each(function(i,e){
                var amt = $(this).val();
                t += parseFloat(amt);
            });
            $('#payable_amount').val(t);
            $('#paid_amount').val(t);
            $('#grand_total').val(t);
            $('#discount_amount').val(0);
        }

        function salePriceSum(){
            console.log('salePriceSum')
            var t = parseFloat(0);
            $('.sale_price').each(function(i,e){
                var amt = $(this).val();
                t += parseFloat(amt);
            });
            $('#grand_total').val(t);
        }

        function discountAmount(){
            console.log('discountAmount')
            var total = $('#payable_amount').val();
            var paid_amount = parseFloat(total) - parseFloat($('#discount_amount').val());
            $('#paid_amount').val(paid_amount);
        }


    </script>
@endpush

