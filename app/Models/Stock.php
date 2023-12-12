<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function purchase(){
        return $this->belongsTo(Purchase::class,'purchase_id');
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    public function created_by_user(){
        return $this->belongsTo(User::class,'created_by_user_id');
    }
}
