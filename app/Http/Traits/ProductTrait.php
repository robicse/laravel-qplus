<?php
namespace App\Http\Traits;

use App\Models\Product;

trait ProductTrait{
    function check_trait(){
        return 'found trait';
    }

    function getProductNameByProductId($id){
        return Product::where('id',$id)->pluck('name')->first();
    }
}
?>
