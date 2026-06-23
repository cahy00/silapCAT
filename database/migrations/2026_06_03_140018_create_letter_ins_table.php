<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_ins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_letter_id')->constrained()->onDelete('cascade');
            $table->foreignId('departement_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('date_letter');
            $table->date('date_in');
            $table->string('origin_letter');
            $table->string('properties_letter');
            $table->string('file')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_ins');
    }
};
