<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateSubAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $info = User::latest()->first();
        if (is_null($info)) {
            $subAdmin = new User();
            $subAdmin->name = 'Sub Admin';
            $subAdmin->user_type = 'Sub Admin';
            $subAdmin->phone = '0177100000';
            $subAdmin->email = 'subadmin@gmail.com';
            $subAdmin->password = Hash::make('123456');
            $subAdmin->created_by_user_id = '1';
            $subAdmin->updated_by_user_id = '1';
            $subAdmin->status = '1';
            if ($subAdmin->save()) {
                $role = Role::create(['name' => 'Sub Admin']);
                $superAdmin->assignRole('Sub Admin');
                $permission = Permission::pluck('name');
                $role = Role::wherename('Sub Admin')->first();
                $role->syncPermissions($permission);
            }
        } else {
            $subAdmin = User::first();
            $subAdmin->assignRole('Sub Admin');
            $permission = Permission::pluck('name');
            $role = Role::wherename('Sub Admin')->first();
            $role->syncPermissions($permission);
        }
    }
}
