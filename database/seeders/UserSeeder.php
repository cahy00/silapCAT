<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
					'name' => 'Sulistyo Cahyo G',
					'email' => 'cgumilang48@gmail.com',
					'nip' => '199806162022031006',
					'password' => bcrypt('5ul15tyoc4hY0!'),
					'remember_token' => Str::random(10),
					'email_verified_at' => now(),
					// 'role' => 'admin',
				]);

    }
}
