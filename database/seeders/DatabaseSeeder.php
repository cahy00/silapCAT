<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Document;
use App\Models\Type;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

				$this->call(UserSeeder::class);
				Type::create([
					'user_id' => 3,
					'title' => 'Nota Dinas'
				]);

				Document::create([
					'user_id' => 3,
					'type_id' => 1,
					'name' => 'test',
					'date' => 'dsd',
					'number' => 'fdsf',
					'file' => 'sdfsd',
					'jenis_surat' => 'surat_masuk'
				]);
				

    }
}
