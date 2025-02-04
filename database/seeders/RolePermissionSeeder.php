<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name' => 'admin']);
        $pimpinan = Role::create(['name' => 'pimpinan']);
        $operator = Role::create(['name' => 'operator']);
        $user = Role::create(['name' => 'user']);

        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'manage reports']);
        Permission::create(['name' => 'approve reports']);

        $admin->givePermissionTo(['view reports', 'manage reports', 'approve reports']);

        $user = User::find(1); // Ganti dengan ID user yang ingin diberi role
        $user->assignRole('admin');

    }
}
