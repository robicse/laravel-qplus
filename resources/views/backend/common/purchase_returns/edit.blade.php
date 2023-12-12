@extends('backend.layouts.master')
@section('title', 'Package Update')
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
                    <h1>Package</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Package</li>
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
                            @can('packages-create')
                            <h3 class="card-title">Package Create</h3>
                            <div class="float-right">
                                <a href="{{ route(Request::segment(1) . '.packages.index') }}">
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
                            {!! Form::model($package, [
                                'route' => [Request::segment(1) . '.packages.update', $package->id],
                                'method' => 'PATCH',
                                'files' => true,
                            ]) !!}
                            @include('backend.common.packages.form')
                            <div class="col-lg-12 col-md-12 ">
                                <div id="dynamic" class="row card-info  card border  customcontent" >
                                    <table class="table table-responsive" id="table1">
                                        <thead>
                                            <tr>
                                                <th style="width: 4%">Category</th>
                                                <th style="width: 20%">
                                                    Product <span class="required">*</span>
                                                </th>
                                                <th style="width: 9%">Quantity</th>
                                                <th style="width: 10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemlist" class="neworderbody">
                                            @if(count($packageProducts) > 0)
                                            @foreach($packageProducts as $key => $packageProduct)
                                            @php
                                                $current_row = $key+1;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div>
                                                        <select class="form-control category_id select2"
                                                            name="category_id[]" required id="category_id_{{$current_row}}"
                                                            onchange="getCategoryVal({{$current_row}},this);" style="width: 100% !important">
                                                            <option value="">Select Category</option>
                                                            @if(count($categories) > 0)
                                                                @foreach($categories as $category)
                                                                <option value="{{ $category->id }}" {{ $packageProduct->product->category->id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <input type="hidden" class="form-control" name="package_product_id[]" value="{{$packageProduct->id}}" >
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control product_id select2"
                                                        name="product_id[]" id="product_id_{{$current_row}}"
                                                        onchange="getProductVal({{$current_row}},this);"
                                                        required style="width: 100% !important">
                                                        <option value="">Select  Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{$product->id}}" {{$product->id == $packageProduct->product_id ? 'selected' : ''}}>{{$product->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="input-sm text-right form-control" type="number"
                                                        name="qty[]" id='quantity_id_{{$current_row}}' value="{{ $packageProduct->qty }}"
                                                        placeholder="0.00" data-cell="D1" step="any" min="0"
                                                        max="99999999999999" required data-format="0[.]00">
                                                </td>
                                                <td>
                                                    @if(count($packageProducts) != $current_row)
                                                        <input type="button" class="btn btn-danger onlyThisDelete float-left" value="x" title="Remove This Row">

                                                    @else
                                                        <span class="d-inline-flex"><input type="button"  class="btn btn-success addProduct" value="+"> <input type="button" class="btn btn-danger delete float-left" style="margin-left: 5px" value="x" title="Remove This Product"></span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
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
                        '<td width="12%"><div><select  class="form-control category_id select2" name="category_id[]" id="category_id_' +
                    n + '" onchange="getCategoryVal(' + n + ',this);" required>' + category +
                    '</select></div></td>' +
                    '<td><select class="form-control product_id select2"  name="product_id[]" id="product_id_' +
                    n + '" onchange="getProductVal(' + n +
                    ',this);" required></select> </td>' +
                    '<td width="12%"><input type="number"  class="input-sm text-right form-control" name="qty[]" id="quantity_id_' +
                    n +
                    '" required   step="any" min="0" max="99999999999999" placeholder="0.00" data-cell="D' +
                    n +
                    '" data-format="" data-format="0[.]00"></td>' +
                    '<td><span class="d-inline-flex"><input type="button"  class="btn btn-success addProduct" value="+"> <input type="button" class="btn btn-danger delete float-left" style="margin-left: 5px" value="x" title="Remove This Product"></span></td>' +
                    '</tr>';
                $('#itemlist').append(tr);
                $('.select2').select2();
                $('#product_id_' + n).select2('open').trigger('select2:open');
            });

            //new item
            $('#itemlist').delegate('.onlyThisDelete', 'click', function() {
                $(this).parent().parent().remove();
            });

            $('#itemlist').delegate('.delete', 'click', function() {
                $(this).parent().parent().parent().remove();
            });
        });

        function getCategoryVal(row, sel) {
            var current_row = row;
            let current_category_id = sel.value;
            if ($('.category_id').length > 1) {
                var same_category_count = 0;
                var all_category_id = [];
                $('.category_id').each(function(i,e){
                    all_category_id.push(e.value);
                });
                let counter = 0;
                for (category_id of all_category_id) {
                    if (category_id == current_category_id) {
                        counter++;
                    }
                };
                if(counter > 1){
                    $(("#category_id_" + current_row)).html('');
                    alert('You selected same category, Please selected another category!');
                    var category_list = '<option value"" selected>Select Category</option>' + '<?php foreach($categories as $category){ echo "<option value=\'$category->id\'>$category->name</option>"; } ?>';
                    $("#category_id_" + current_row).html(category_list);
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

        function getProductVal(row, sel) {
            var current_row = row;
            let current_product_id = sel.value;
            if ($('.product_id').length > 1) {
                var same_product_count = 0;
                var all_product_id = [];
                $('.product_id').each(function(i,e){
                    all_product_id.push(e.value);
                });
                let counter = 0;
                for (product_id of all_product_id) {
                    if (product_id == current_product_id) {
                        counter++;
                    }
                };
                if(counter > 1){
                    $(("#product_id_" + current_row)).html('');
                    alert('You selected same category product, Please selected another category product!');
                    // $("#product_id_" + current_row).html($('.product_id').html());
                }
            }
        }
    </script>
@endpush

