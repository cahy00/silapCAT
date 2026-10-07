<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('events')->where('status', 'aktif')->update(['status' => 'active']);
        DB::table('events')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('events')->where('status', 'dibatalkan')->update(['status' => 'cancelled']);

        Artisan::call('events:sync-status');
    }

    public function down(): void
    {
        // Normalisasi data tidak dikembalikan.
    }
};
