<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use App\Helpers\ErrorTryCatch;
use Illuminate\Support\Facades\Storage;
use Image;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BusinessSettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:business-settings-list', ['only' => ['index', 'show']]);
        //$this->middleware('permission:business-settings-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:business-settings-edit', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:business-settings-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $title = BusinessSetting::where('type', 'title')->first();
        $vat_no = BusinessSetting::where('type', 'vat_no')->first();
        $vat_percent = BusinessSetting::where('type', 'vat_percent')->first();
        $system_default_currency = BusinessSetting::where('type', 'system_default_currency')->first();
        $currencies = Currency::where('status', '1')->get();
        $unit_price_variant = BusinessSetting::where('type', 'unit_price_variant')->first();
        $company_contact_no = BusinessSetting::where('type', 'company_contact_no')->first();
        $company_address = BusinessSetting::where('type', 'company_address')->first();
        $company_logo = BusinessSetting::where('type', 'company_logo')->first();
        $trade_license = BusinessSetting::where('type', 'trade_license')->first();
        $trade_license_expired_date = BusinessSetting::where('type', 'trade_license_expired_date')->first();
        $taxpayer_name = BusinessSetting::where('type', 'taxpayer_name')->first();
        $effective_registration_date = BusinessSetting::where('type', 'effective_registration_date')->first();
        $cr_license_contract_no = BusinessSetting::where('type', 'cr_license_contract_no')->first();
        $taxperiod = BusinessSetting::where('type', 'taxperiod')->first();
        $company_email = BusinessSetting::where('type', 'company_email')->first();
        $time_zone = BusinessSetting::where('type', 'time_zone')->first();

        return view('backend.common.business_settings.index', compact('vat_no', 'vat_percent', 'title', 'system_default_currency', 'currencies', 'unit_price_variant','company_contact_no','company_address', 'company_logo','trade_license','trade_license_expired_date','taxpayer_name','effective_registration_date','cr_license_contract_no','taxperiod','company_email','time_zone'));
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }

    public function businessSettingsUpdate(Request $request)
    {
        // dd($request->id);

        $singaleRowdata = BusinessSetting::find($request->id);
        $logo_image = $request->file('logo');
        $trade_license_image = $request->file('trade_license_image');

        if (isset($logo_image)) {
            //make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $logoImageName = $currentDate . '-' . uniqid() . '.' . $logo_image->getClientOriginalExtension();

            if (Storage::disk('public')->exists($singaleRowdata->value)) {
                Storage::disk('public')->delete($singaleRowdata->value);
            }

            $proLogoImage = Image::make($logo_image)->resize(200, 200)->save($logo_image->getClientOriginalExtension());
            Storage::disk('public')->put('uploads/business_setting/' . $logoImageName, $proLogoImage);
            $singaleRowdata->value = 'uploads/business_setting/' . $logoImageName;
            $singaleRowdata->save();
        }else if(isset($trade_license_image)){
            $currentDate = Carbon::now()->toDateString();
            $tradeImageName = $currentDate . '-' . uniqid() . '.' . $trade_license_image->getClientOriginalExtension();

            if (Storage::disk('public')->exists($singaleRowdata->value)) {
                Storage::disk('public')->delete($singaleRowdata->value);
            }

            $proTradeImage = Image::make($trade_license_image)->resize(200, 200)->save($trade_license_image->getClientOriginalExtension());
            Storage::disk('public')->put('uploads/business_setting/' . $tradeImageName, $proTradeImage);
            $singaleRowdata->value = 'uploads/business_setting/' . $tradeImageName;
            $singaleRowdata->save();
        }
        elseif($request->value=='Asia/Riyadh' || $request->value=='Asia/Dhaka'){
           
            $path = app()->environmentFilePath();
            $type="APP_TimeZone";
            if (file_exists($path)) {
                $val = trim($request->value);
              
               if (is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0) {
                
                file_put_contents($path, str_replace(
                        $type . '='. env($type),
                        $type . '=' . $val,
                        file_get_contents($path)
                    ));
                    
                } else {
                   
                    file_put_contents($path, file_get_contents($path) . "\r\n" . $type . '=' . $val);
                   
                }
            }
            $singaleRowdata->value = $request->value;
            $singaleRowdata->save();


        }
        
        else {
            $singaleRowdata->value = $request->value;
            $singaleRowdata->save();
           

        }

        if (!empty($singaleRowdata)) {
            return 1;
        } else {
            return 0;
        }
    }
}
