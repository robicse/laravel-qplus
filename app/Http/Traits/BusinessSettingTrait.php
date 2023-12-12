<?php
namespace App\Http\Traits;

use App\Models\BusinessSetting;

trait BusinessSettingTrait{
    function getSystemStartYear(){
        return BusinessSetting::where('type','start_year')->pluck('value')->first();
    }

    function getBusinessSettingsInfo(){
        return BusinessSetting::select('type','value')->get();
    }
}
?>
