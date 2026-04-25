<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('location_surveys', function (Blueprint $table) {
            $table->renameColumn('cctv_count', 'room_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_surveys', function (Blueprint $table) {
            $table->renameColumn('room_count', 'cctv_count');
        });
    }
};
