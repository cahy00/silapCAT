<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('type_id')->references('id')->on('types')->cascadeOnDelete();
            $table->string('name');
            $table->string('tgl_distribusi');
            $table->string('date');
            $table->string('disposisi');
            $table->string('number');
            $table->string('asal');
            $table->string('sifat');
            $table->string('file');
            $table->enum('jenis_surat', ['surat_masuk', 'surat_keluar']);
            $table->enum('unit', ['inka', 'pdsk', 'mutasi', 'pensiun', 'tu', 'keuangan', 'kepegawaian', 'umum']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
