<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Package extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    // public function category(){
    //     return $this->belongsTo(Category::class,'category_id');
    // }

    public function created_by_user(){
        return $this->belongsTo(User::class,'created_by_user_id');
    }
}
