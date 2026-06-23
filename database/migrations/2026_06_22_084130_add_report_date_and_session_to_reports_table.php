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
            $table->date('report_date')->nullable()->after('event_id');
            $table->string('session_name')->nullable()->after('report_date');
            
            // Adding a unique constraint to prevent duplicates for same event, date, and session
            $table->unique(['event_id', 'report_date', 'session_name'], 'unique_event_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('unique_event_report');
            $table->dropColumn(['report_date', 'session_name']);
        });
    }
};
