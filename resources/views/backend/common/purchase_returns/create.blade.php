@extends('backend.layouts.master')
@section('title', 'Purchase Return Create')
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
                    <h1>Purchase Return</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Purchase Return</li>
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
                            @can('purchase-returns-create')
                            <h3 class="card-title">Purchase Return Create</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.purchase-returns.index') }}">
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
                            $purchase = '';
                            @endphp
                            {!! Form::open(['route' => Request::segment(1) . '.purchase-returns.store', 'method' => 'POST', 'files' => true]) !!}
                            @include('backend.common.purchase_returns.form')
                            <div class="col-lg-12 col-md-12 ">
                                <div id="dynamic" class="row card-info  card border  customcontent" >
                                    <table class="table table-responsive" id="table1">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%">Category <span class="required">*</span></th>
                                                <th style="width: 20%">Product <span class="required">*</span></th>
                                                <th style="width: 20%">Quantity <span class="required">*</span></th>
                                                {{-- <th>Buy Price (Unit) <span class="required">*</span></th>
                                                <th>Min Sell Price (Unit) <span class="required">*</span></th> --}}
                                                <th style="width: 20%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemlist">
                                            <tr>
                                                <td style="width: 20%">
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
                                                <td style="width: 20%">
                                                    <select class="form-control product_id select2"
                                                        name="product_id[]" id="product_id_1"
                                                        required>
                                                    </select>
                                                </td>
                                                <td style="width: 20%">
                                                    <input class="input-sm text-right form-control qty" type="number" onkeyup="quantitySum()"
                                                        name="qty[]" id='quantity_id_1'
                                                        placeholder="0.00" data-cell="D1" step="any" min="0"
                                                        max="99999999999999" required data-format="0[.]00">
                                                </td>
                                                {{-- <td>
                                                    <input type="number"  step="any"
                                                        class="input-sm text-right buy_price
                                                    form-control"
                                                        placeholder="0.0000" name="buy_price[]" onkeyup="buyPriceSum()"
                                                        id='buy_price_id_1' required data-format="0[.]00"
                                                        data-cell="C1" step="any" min="0"
                                                        max="99999999999999">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control input-sm text-right sell_price" onkeyup="sellPriceSum()"
                                                        name="sell_price[]" placeholder="0.00" data-cell="F1"
                                                        data-format="0[.]00" data-formula=""
                                                        step="any" min="0" max="99999999999999">
                                                </td> --}}
                                                <td style="width: 20%">
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
                                                {{-- <td>Total Buy Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="total_buy_amount" id="total_buy_amount" placeholder="0.00" data-cell=""
                                                    data-format="0[.]00" data-formula=""
                                                    step="any" min="0" max="99999999999999">
                                                </td>
                                                <td>Total Sell Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="grand_total" id="grand_total" placeholder="0.00" data-cell=""
                                                    data-format="0[.]00" data-formula=""
                                                    step="any" min="0" max="99999999999999">
                                                </td>
                                                <td>Discount Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"  onkeyup="discountAmount()"
                                                    name="discount_amount" id="discount_amount" placeholder="0.00"
                                                    step="any" min="0" max="99999999999999">
                                                </td>
                                                <td>Paid Amount: <span class="required">*</span>
                                                    <input type="text" class="form-control input-sm text-right"
                                                    name="paid_amount" id="paid_amount" placeholder="0.00"
                                                    step="any" min="0" max="99999999999999">
                                                </td> --}}
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
                var n = ($('#itemlist tr').length - 0) + 1;
                var tr =
                    '<tr>' +
                        '<td style="width: 20%"><div><select  class="form-control category_id select2" name="category_id[]" id="category_id_' +
                    n + '" onchange="getCategoryVal(' + n + ',this);" required>' + category +
                    '</select></div></td>' +
                    '<td style="width: 20%"><select class="form-control product_id select2"  name="product_id[]" id="product_id_' +
                    n + '" required></select> </td>' +
                    '<td style="width: 20%"><input type="number"  class="input-sm text-right form-control qty" onkeyup="quantitySum()" name="qty[]" id="quantity_id_' +
                    n +
                    '" required   step="any" min="0" max="99999999999999" placeholder="0.00" data-cell="" data-format="" data-format="0[.]00"></td>' +
                    '<td style="width: 20%"><span class="d-inline-flex"><input type="button"  class="btn btn-success addProduct" value="+"> <input type="button" class="btn btn-danger delete float-left" style="margin-left: 5px" value="x" title="Remove This Product"></span></td>' +
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
            console.log('111')
            var current_row = row;
            var current_category_id = sel.value;
            console.log('current_category_id',current_category_id)

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
                    console.log('res', res)
                    $(("#product_id_" + current_row)).html(res.data.productOptions);
                },
                error: function(err) {
                    console.log(err)
                }
            })
        }

        function quantitySum(){
            var t = parseInt(0);
            $('.qty').each(function(i,e){
                var amt = $(this).val();
                t += parseInt(amt);
            });
            $('#total_quantity').val(t);
        }

        function buyPriceSum(){
            console.log('ss')
            var t = parseFloat(0);
            $('.buy_price').each(function(i,e){
                var amt = $(this).val();
                t += parseFloat(amt);
            });
            $('#total_buy_amount').val(t);
            $('#paid_amount').val(t);
        }

        function sellPriceSum(){
            console.log('ss')
            var t = parseFloat(0);
            $('.sell_price').each(function(i,e){
                var amt = $(this).val();
                t += parseFloat(amt);
            });
            $('#grand_total').val(t);
        }

        function discountAmount(){
            var total = $('#total_buy_amount').val();
            var paid_amount = parseFloat(total) - parseFloat($('#discount_amount').val());
            $('#paid_amount').val(paid_amount);
        }


    </script>
@endpush

