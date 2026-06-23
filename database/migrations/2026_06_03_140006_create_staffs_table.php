<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('departement_id')->constrained()->onDelete('cascade');
            $table->string('position');
            $table->string('nip');
            $table->string('photo')->nullable();
            $table->string('lhkpn')->nullable();
            $table->enum('category', ['kepala_bkn', 'jptm', 'kepala_regional', 'administrator', 'pengawas', 'fungsional']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
