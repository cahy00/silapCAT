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
        // 1. Add start_date and end_date to events table
        Schema::table('events', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('status');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // 2. Create event_location_institutions table
        Schema::create('event_location_institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->integer('participants_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_location_institutions');

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
