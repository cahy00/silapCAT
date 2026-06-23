<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('question_category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ministry_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('nip')->nullable();
            $table->string('instansi')->nullable();
            $table->string('wa');
            $table->text('pesan');
            $table->string('status')->default('Belum Dijawab');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
