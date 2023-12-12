<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateAdmin extends Seeder
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
            $admin = new User();
            $admin->user_type = 'Admin';
            $admin->name = 'Admin';
            $admin->username = 'admin';
            $admin->phone = '0177100000';
            $admin->email = 'admin@gmail.com';
            $admin->password = Hash::make('123456');
            $admin->created_by_user_id = '1';
            $admin->updated_by_user_id = '1';
            $admin->status = '1';
            if ($admin->save()) {
                $role = Role::create(['name' => 'Admin']);
                $admin->assignRole('Admin');
                $permission = Permission::pluck('name');
                $role = Role::wherename('Admin')->first();
                $role->syncPermissions($permission);
            }
        } else {
            $admin = User::first();
            $admin->assignRole('Admin');
            $permission = Permission::pluck('name');
            $role = Role::wherename('Admin')->first();
            $role->syncPermissions($permission);
        }
    }
}
