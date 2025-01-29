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
					'password' => bcrypt('12'),
					'remember_token' => Str::random(10),
					'email_verified_at' => now(),
					'role' => 'admin',
				]);

				// User::create([
				// 	'name' => 'PDSK ',
				// 	'email' => 'pdsk@gmail.com',
				// 	'nip' => '199806162022031002',
				// 	'password' => bcrypt('12345678'),
				// 	'remember_token' => Str::random(10),
				// 	'email_verified_at' => now(),
				// 	'role' => 'pdsk',
				// ]);

				// User::create([
				// 	'name' => 'INKA ',
				// 	'email' => 'inka@gmail.com',
				// 	'nip' => '199806162022031001',
				// 	'password' => bcrypt('12345678'),
				// 	'remember_token' => Str::random(10),
				// 	'email_verified_at' => now(),
				// 	'role' => 'inka',
				// ]);

				// User::create([
				// 	'name' => 'INKA ',
				// 	'email' => 'inkasquad@gmail.com',
				// 	'nip' => '199806162022031003',
				// 	'password' => bcrypt('12345678'),
				// 	'remember_token' => Str::random(10),
				// 	'email_verified_at' => now(),
				// 	'role' => 'inka',
				// ]);

				User::create([
					'name' => 'Dian Violora Nainggolan',
					'email' => 'dianviolora32@gmail.com',
					'nip' => '199003252022032003',
					'password' => bcrypt('199003252022032003'),
					'remember_token' => Str::random(10),
					'email_verified_at' => now(),
					'role' => 'arsip_digital',
				]);
    }
}
