@extends('backend.layouts.master')
@section('title', 'blank Sale Create')
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
                    <h1>blank Sale Create </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">blank Sale</li>
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
                            <h3 class="card-title">blank Sale </h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.blank-sales.index') }}">
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
                            {!! Form::open(['route' => Request::segment(1) . '.blank-sales.store', 'method' => 'POST', 'files' => true]) !!}
                            <div class="row">
                                @include('backend.common.blank_sales.form')

                                <div class="row">&nbsp;</div>
                                <div class="col-lg-12 col-md-12">
                                    <div id="dynamic" class="row bg-light">
                                        <table class="table table-responsive">
                                            <thead>
                                                <tr>
                                                    <th style="width: 24%">
                                                        Product <span class="required">*</span>
                                                    </th>
                                                    <th style="width: 8%">Unit</th>
                                                    <th style="width: 10%">Qty</th>
                                                    <th style="width: 15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemlist">
                                                <tr>
                                                    <td width="24%">
                                                        <select class="form-control product_id select2" name="product_id[]"
                                                            id="product_id_1" onchange="getval(1,this);" required>
                                                        </select>
                                                    </td>
                                                    <td width="12%">
                                                        <div>
                                                            <select class="form-control unit_id select2" name="unit_id[]"
                                                                required id="unit_id_1" onchange="getUnitVal(1,this);">
                                                                <option value="">Select Unit</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td width="10%">
                                                        <input class="input-sm text-right form-control quantity" type="number"
                                                            name="qty[]" onblur="getQty(1,this);" id='qty_id_1'
                                                            placeholder="0.00" data-cell="D1" step="any"
                                                            min="0" max="9999999999999999" required
                                                            data-format="0[.]00">
                                                        <span id="show_stock_qty_1"></span>
                                                    </td>
                                                    <td width="15%">
                                                        <input type="button" class="btn btn-success addProduct"
                                                            value="+">
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                        <div class="row">&nbsp;</div>
                                    </div>
                                </div>

                                <table class="table table-sticky-bg table-responsive"
                                    style="position: sticky;
                                bottom: 0; z-index: 999;">
                                    <tr>
                                        <th>Total Qty</th>
                                        <td>
                                            <input class="input-sm text-right form-control" type="number"
                                                name="total_quantity" id='total_quantity'
                                                placeholder="0.00" data-cell="" step="any" min="0"
                                                max="99999999999999" required data-format="0[.]00" readonly>
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
                var product = $('.product_id').html();
                var n = ($('#itemlist tr').length - 0) + 1;
                var tr =
                    '<tr>' +
                    '<td><select class="form-control product_id select2" name="product_id[]" id="product_id_' +
                    n + '" onchange="getval(' + n +
                    ',this);" required ></select></td>' +

                    '<td width="12%"><div><select class="form-control unit_id select2" name="unit_id[]" id="unit_id_' +
                    n + '" onchange="getUnitVal(' + n +
                    ',this);" required > required' +
                    '</select></div></td>' +

                    '<td width="12%"><input type="number" class="input-sm text-right form-control quantity" name="qty[]" id="qty_id_' +
                    n + '" required  step="any" placeholder="0.00" data-cell="D' + n +
                    '" step="any" min="0" max="9999999999999999" data-format="0[.]00" onblur="getQty(' +
                    n + ',this);"><span id="show_stock_qty_' + n + '"></span></td>' +

                    '<td><span class="d-inline-flex"><input type="button" class="btn btn-success addProduct" value="+" title="Add New"> <input type="button" class="btn btn-danger delete float-left" style="margin-left: 5px" value="x" title="Remove This Product"></span></td>' +
                    '</tr>';

                $('#itemlist').append(tr);


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

            $(document).on('keydown', '#itemlist tr:last .sale_price', function(e) {
                if (e.keyCode == 9) {
                    var product = $('.product_id').html();
                    var n = ($('#itemlist tr').length - 0) + 1;
                    var tr =
                        '<tr><td><select class="form-control product_id select2" name="product_id[]" id="product_id_' +
                        n + '" onchange="getval(' + n +
                        ',this);" required ></select></td>' +

                        '<td width="12%"><div><select class="form-control unit_id select2" name="unit_id[]" id="unit_id_' +
                        n + '" onchange="getUnitVal(' + n +
                        ',this);" required > required' +
                        '</select></div></td>' +

                        '<td width="12%"><input type="number" class="input-sm text-right form-control quantity" name="qty[]" id="qty_id_' +
                        n + '" required  step="any" placeholder="0.00" data-cell="D' + n +
                        '" step="any" min="0" max="9999999999999999" data-format="0[.]00" onblur="getQty(' +
                        n + ',this);"><span id="show_stock_qty_' + n + '"></span></td>' +

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
                }
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

            $('#name,#date').on("change", function() {
                if ($('#name').val() !== null) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url(Request::segment(1) . '/check-customer-limit') }}",
                        data: {
                            customer_user_id: $('#name').val(),
                            date: $('#date').val(),
                        },
                        success: function(data) {
                            $('#customerDue').html(data.dueInfo);
                            if (data.status == 'credit_off') {
                                $('#submitbtn').prop("disabled", true);
                                Swal.fire({
                                    position: 'center',
                                    icon: 'info',
                                    title: data.message,
                                    showConfirmButton: false,
                                    showCloseButton: true
                                })

                            } else {
                                $('#submitbtn').prop("disabled", false);
                            }

                        }
                    });
                }

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
                url: "{{ URL(Request::segment(1) . '/blank-sale-relation-data') }}",
                method: "get",
                data: {
                    current_product_id: current_product_id,
                    store_id: store_id
                },

                success: function(res) {
                    console.log('res',res)
                    $(("#unit_id_" + current_row)).html(res.data.unitOptions);
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

        //onkeyup
        function getQty(row, sel) {
            quantitySum();
        }

        //automatically call after two seconds/
        //tab index changing
        $('#date').on("keydown", function() {
            $('#product_id_1').select2('open').trigger('select2:open');
        });


        function showProductForm() {
            var page = "{{ url(Request::segment(1) . '/stock-transfer-warehouse-to-van') }}";
            var myWindow = window.open(page, "_blank", "scrollbars=yes,width=700,height=1000,top=30");
            // focus on the popup //
            myWindow.focus();
        }


    </script>
@endpush
