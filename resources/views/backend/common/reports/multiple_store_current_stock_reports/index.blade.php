@extends('backend.layouts.master')
@section('title', 'Store Current Stock Report')
@push('css')

    <link rel="stylesheet" href="{{ asset('backend/css/custom.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Multiple Store Current Stock  Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route(Request::segment(1) . '.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Multiple Store Current Stock</li>
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
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {!! Form::open(['url' => Request::segment(1) . '/multiple-store-current-stock-report']) !!}
                            <div class="row justify-content-center">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select Store:</label>
                                        {!! Form::select('store_id[]',$stores,@Auth::user()->store_id?:null, ['class' => 'form-control select2','id'=>'store_id','required','multiple']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select Product:</label>
                                        {{-- {!! Form::select('product_id[]',$products,null, ['class' => 'form-control select2','id'=>'product_id','required','multiple']) !!} --}}
                                        <select class="form-control select2" name="product_id[]" id="product_id" required multiple autofocus>
                                            <option value="All">All Product</option>
                                            @if(count($products))
                                                @foreach($products as $product)
                                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-1">
                                    <div class="form-group ">
                                        <br>
                                        <button class="btn btn-primary  mt-2">Submit</button>
                                    </div>
                                </div>
                            </div>
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

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.select2').select2()


          });

    </script>

@endpush
