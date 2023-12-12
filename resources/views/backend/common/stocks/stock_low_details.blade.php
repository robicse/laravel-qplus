@extends('backend.layouts.master')
@section('title', 'stock_lows Lists')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i> Stock Low</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                {{--                <li class="breadcrumb-item"><a class="btn btn-warning" href="{{ route('stock.export') }}">Export Data</a></li> --}}
            </ul>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Stock Low</h3>
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">#Id</th>
                                <th width="12%">Store</th>
                                <th width="12%">Product</th>
                                <th width="12%">Product Stock Low Qty</th>
                                <th width="12%">Current Stock Low Qty</th>
                                {{-- <th width="12%">Stock In Now</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stock_lows as $key => $stock)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $stock['store_name'] }}</td>
                                    <td>{{ $stock['product_name'] }}</td>
                                    <td>{{ $stock['stock_low_qty'] }}</td>
                                    <td>{{ $stock['current_stock_low_qty'] }}</td>
                                    {{-- <td>
                                            @if ($stock->product->product_type == 'Finish Goods')
                                                <a href="{!! route('productPurchases.create') !!}" class="btn btn-sm btn-primary" type="button">Purchases Now</a>
                                            @else
                                                <a href="{!! route('productPurchases.create') !!}" class="btn btn-sm btn-primary" type="button">Purchases Now</a>
                                            @endif
                                        </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="tile-footer">
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
