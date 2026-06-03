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
        Schema::table('event_institutions', function (Blueprint $table) {
            $table->dropColumn(['participants_count', 'start_date', 'end_date']);
        });

        Schema::table('event_locations', function (Blueprint $table) {
            $table->integer('participants_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_locations', function (Blueprint $table) {
            $table->dropColumn(['participants_count', 'start_date', 'end_date']);
        });

        Schema::table('event_institutions', function (Blueprint $table) {
            $table->integer('participants_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }
};
