<?php
namespace App\Http\Traits;

use App\Models\Currency;

trait CurrencyTrait{
    function getCurrencyInfoByDefaultCurrency(){
        return Currency::where('default_status',1)->select('symbol')->first();
        // return Currency::join('business_settings','currencies.id','business_settings.value')
        // ->where('business_settings.type','system_default_currency')
        // ->select('currencies.symbol')
        // ->first();
    }
}
?>
