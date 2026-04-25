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
        Schema::create('location_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->integer('pc_count')->default(0);
            $table->integer('cctv_count')->default(0);
            $table->enum('feasibility_status', ['feasible', 'not_feasible', 'conditional'])->nullable();
            $table->string('surveyor_name')->nullable();
            $table->date('survey_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('survey_document')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_surveys');
    }
};
