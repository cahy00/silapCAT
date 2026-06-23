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
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('event_location_id')->nullable()->after('event_id')->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('session_name')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['event_location_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['event_location_id', 'user_id']);
        });
    }
};
