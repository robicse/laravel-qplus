@include('backend/common/report/header')
<div class="middle_body">
    <div class="wrapper_date_operator">
        <div class="wrapper_date_operator_date">
            Date:  {{ date('d-m-Y') }}
        </div>
        <div class="wrapper_date_operator_date_type">
            Type: Stock Status<div class="wrapper_date_operator_date_type">
            </div>
        </div>
        <div class="wrapper_date_operator_operator">
            Operator Name:
        </div>
    </div>
    <div class="wrapper_client_information">
        <p><b class="clientb">Purchase date </b>:  </p>
        <p><b class="clientb">Comapany Name </b>:  </p>
        <p><b class="clientb">Mobile </b>:  </p>
    </div>
    <div style="width:100%; height:auto; min-height:500px;">
        @if(!empty($data))
        <table class="table_of_pdf" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th style="width:10px">Sl</th>
                    <th>Category Name </th>
                    <th>Product Name </th>
                    <th>Quantity </th>
                    <th>Buy Price </th>
                    <th>Sell Price </th>
                </tr>
            </thead>
            <tbody>
			 @if(!empty($data))
                @php
				$i=1;
                @endphp
				@foreach($data as $key){
                <tr>
                    {{-- <td>{{ $i++ }}</td>
                    <td>{{ $key->category }}</td>
                    <td>{{ $key->product_name }}</td>
                    <td>{{ $key->quantity }}</td>
                    <td>{{ $key->buy_price }}</td>
                    <td>{{ $key->sell_price }}</td> --}}
                </tr>
                @endforeach
            @endif
            </tbody>
            <tfoot>
                {{-- <tr>
                    <td colspan="3"> Total Quantity</td>
                    <td style="text-align:left;" colspan="3">{{ $purchase_data->total_quantity }}</td>
                </tr>
                <tr>
                    <td colspan="3"> Total Buy Amount </td>
                    <td style="text-align:left;" colspan="3">{{ $purchase_data->total_buy_amount }}/- </td>
                </tr>
                <tr>
                    <td colspan="3"> Total Sell Amount </td>
                    <td style="text-align:left;" colspan="3">{{ $purchase_data->total_sell_amount }}/- </td>
                </tr>
                <tr>
                    <td colspan="3"> Discount Amount </td>
                    <td style="text-align:left;" colspan="3">{{ $purchase_data->discount_amount }}/- </td>
                </tr>
                <tr>
                    <td colspan="3"> Paid Amount </td>
                    <td style="text-align:left;" colspan="3">{{ $purchase_data->paid_amount }}/-</td>
                </tr>
                <tr>
                    <td colspan="3">Due Amount </td>
                    <td style="text-align:left;" colspan="3">{{ $purchase_data->total_buy_amount - $purchase_data->paid_amount }}/-</td>
                </tr> --}}
            </tfoot>
        </table>
        @endif
    </div>
</div>
</body>

</html>
