<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator']);
        $pimpinanRole = Role::firstOrCreate(['name' => 'pimpinan']);

        // Create initial Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@silapcat.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole($adminRole);
        
        // Create initial Operator
        $operator = User::firstOrCreate(
            ['email' => 'operator@silapcat.com'],
            [
                'name' => 'Operator System',
                'password' => Hash::make('password'),
            ]
        );
        $operator->assignRole($operatorRole);

        // Create initial Pimpinan
        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@silapcat.com'],
            [
                'name' => 'Pimpinan Unit',
                'password' => Hash::make('password'),
            ]
        );
        $pimpinan->assignRole($pimpinanRole);
    }
}
