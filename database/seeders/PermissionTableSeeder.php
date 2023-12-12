<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $menuJson = file_get_contents(base_path('resources/json/menuSubMenu.json'));
        $menuData= json_decode($menuJson);
        if(!empty($menuData) && isset($menuData)){
            foreach($menuData->menu as $menu){
                // menu
                $menuInfo = Module::wherename($menu->name)->first();
                if (empty($menuInfo)) {
                    Module::create([
                        'parent_menu' => $menu->parent_menu,
                        'name' => $menu->name,
                        'slug' => $menu->slug,
                        'icon' => $menu->icon,
                        'serial' =>$menu->serial,
                        'status' => $menu->status,
                        'open_new_tab' => $menu->open_new_tab
                    ]);
                }

                // sub menu
                if(count($menu->submenu) > 0){
                    foreach($menu->submenu as $subMenu){
                        $subMenuInfo = Module::wherename($subMenu->name)->first();
                        if (empty($subMenuInfo)) {
                            Module::create([
                                'parent_menu' => $subMenu->parent_menu,
                                'name' => $subMenu->name,
                                'slug' => $subMenu->slug,
                                'icon' => $subMenu->icon,
                                'serial' =>$subMenu->serial,
                                'status' => $subMenu->status,
                                'open_new_tab' => $menu->open_new_tab
                            ]);
                        }
                    }
                }
            }

            foreach($menuData->permission as $key => $permission){
                $permission = $permission->name;
                $info = Permission::wherename($permission)->first();
                if (empty($info)) {
                    $strArray = explode('-', $permission);
                    $lastElement = end($strArray);
                    $lastElementWithHyphen = '-' . $lastElement;
                    $afterReplace = str_replace($lastElementWithHyphen, '', $permission);
                    $final_string = lcfirst($afterReplace);
                    $module_id = Module::where('slug', $final_string)->pluck('id')->first();
                    if (!empty($module_id)) {
                        Permission::create([
                            'name' => $permission,
                            'module_id' => $module_id,
                        ]);
                    }
                }
            }
        }





    }
}
