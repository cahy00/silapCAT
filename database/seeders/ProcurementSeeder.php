<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'CASN' => ['CPNS', 'PPPK', 'CPNS & PPPK', 'Sekdin'],
            'Tes Pengembangan Karir' => ['UD', 'UPKP', 'UD/UPKP'],
            'Tes Pengembangan Kompetensi' => ['CACT', 'ProASN'],
            'Lain-lain' => ['Tes Lainnya'],
        ];

        foreach ($categories as $categoryName => $types) {
            $category = \App\Models\ProcurementCategory::create(['name' => $categoryName]);
            foreach ($types as $typeName) {
                $category->procurementTypes()->create(['name' => $typeName]);
            }
        }
    }
}
