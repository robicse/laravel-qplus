<table class="table table-bordered table-striped data-table data-table">
    <thead>
        <tr>
            <th>SL1</th>
            <th>Invoice No</th>
            <th>Referance Name</th>
            <th>Date</th>
            <th>Warehouse</th>
            <th>Customer Name</th>
            <th>Salesman Name</th>
            <th>Customer Vat No</th>
            <th>Total Vat</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($warehouseWiseSaleReports as $sale)
            <tr>
                <td>{{ $loop->index + 01 }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ $business_setting[0]->value }}</td>
                <td>{{ $sale->date }}</td>
                <td>{{ @$warehouseInfo->name }}</td>
                <td>{{ @getCustomerNameById($sale->customer_user_id) }}</td>
                <td>{{ @getSalesmanNameById($sale->salesman_user_id) }}</td>
                <td>{{ $sale->customer_vat_no }}</td>
                <td>{{ $sale->total_vat }}</td>
                <td class="text-right">{{ $sale->grand_total }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><strong> Total : </strong> </td>
            <td class="text-right"> <strong>
                    {{ $warehouseWiseSaleReports->sum('grand_total') }}</strong></td>
        </tr>
    </tfoot>
</table>
