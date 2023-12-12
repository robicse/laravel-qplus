<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceReceipt extends Model
{
    use HasFactory;

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function payment_type(){
        return $this->belongsTo(PaymentType::class);
    }

    public function created_by_user(){
        return $this->belongsTo(User::class,'created_by_user_id');
    }
}
