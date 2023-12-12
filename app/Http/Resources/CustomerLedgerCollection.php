<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerLedgerCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {

                return [
                    'id' => $data->id,
                    'voucher_date' => $data->voucher_date,
                    'orderTypeName' => 'eeee',
                    'paymentTypeName' =>'123',
                    'grand_total' => $data->grand_total,
                    'paid_amount' => $data->paid_amount,
                    'due_amount' => $data->due_amount,
                    'comments' => $data->comments
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
