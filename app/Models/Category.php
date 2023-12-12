<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $fillable=[
        'is_delete','name','created_by_user_id','updated_by_user_id','status'
     ];
    public function subcategory(){
        return $this->hasMany(SubCategory::class);
    }
}
