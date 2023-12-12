<table>
    <tr>
        <td style="width: 33%;" class="text-left small">Date: {{ @$date ? $date : date('Y-m-d') }}</td>
        <td style="width: 33%;" class="text-center small">Type: {{ @$invoice_type }}</td>
        <td style="width: 33%;" class="text-right small">Operator Name: {{ Helper::getOperatorName($created_by_user_id) }}</td>
    </tr>
    <tr>
        <td style="width: 33%;" class="text-left small">
            Time: {{@$created_at ? @$created_at->format('H:i:s A') : date('H:i:s')}}
        </td>
        @if(@$invoice_no)
        <td style="width: 33%;" class="text-center small">Invoice NO: {{ @$invoice_no }}</td>
        @endif
    </tr>
</table>
