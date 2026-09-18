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
        Schema::create('schedule_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Skenario / Draft (misal: "Skenario A - 4 Sesi Max")
            $table->string('event_name')->nullable();
            $table->unsignedSmallInteger('formation_year')->nullable();
            $table->foreignId('procurement_type_id')->nullable()->constrained('procurement_types')->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('items_data'); // Data baris spreadsheet input
            $table->json('results_data')->nullable(); // Data hasil generate schedule matrix & GA assignments
            $table->json('summary_meta')->nullable(); // Metadata ringkasan (total peserta, total hari, dll)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_scenarios');
    }
};
