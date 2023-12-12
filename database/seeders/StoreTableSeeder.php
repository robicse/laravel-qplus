<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Store::latest()->first();
        if (is_null($setting)) {
            $setting = new Store();
            $setting->created_by_user_id = '1';
            $setting->name = "WATER FINE TREATMENT & FILTER'S";
            $setting->location = 'Mirpur-10';
            $setting->phone = '01715-936191,01968773233';
            $setting->email = 'info@waterfinebd.com';
            $setting->website = 'www.waterfinebd.com';
            $setting->address = 'Hazi Tower House# 11/1, Road # 1,Block# Kha,Section # 6,west senpara Parbota, Mirpur,Dhaka-1216';
            $setting->logo = 'uploads/setting/default.png';
            $setting->status = '1';
            $setting->save();
        }
    }
}
