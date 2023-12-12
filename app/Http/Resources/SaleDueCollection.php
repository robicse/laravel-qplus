<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SaleDueCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $sale_due_amount = $data->due_amount;
                $sale_return_invoice_due = SaleReturn::where('sale_id','!=',$data->id)->sum('invoice_due');
                $sale_return_refundable_amount = SaleReturn::where('sale_id','!=',$data->id)->sum('refundable_amount');
                $sale_return_refund_amount = SaleReturn::where('sale_id','!=',$data->id)->sum('refund_amount');
                $sale_return_due_amount = SaleReturn::where('sale_id','!=',$data->id)->sum('due_amount');
                $sale_return_due = SaleReturn::where('sale_id','!=',$data->id)->sum('due_amount');
                $due_amount = $sale_due_amount - $sale_return_invoice_due;
                return [
                    'id' => $data->id,
                    'due_amount' => 0,
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
