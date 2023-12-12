<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlankSaleProduct extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Product::Class);
    }

    public function unit(){
        return $this->belongsTo(Unit::Class);
    }
}
