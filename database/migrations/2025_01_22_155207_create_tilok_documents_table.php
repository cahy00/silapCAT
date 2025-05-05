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
        Schema::create('tilok_documents', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('event_tilok_id')->references('id')->on('event_tiloks')->cascadeOnDelete();
            $table->foreignId('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->foreignId('tilok_id')->references('id')->on('tiloks')->cascadeOnDelete();
            $table->string('document_name');
            $table->string('document_path');
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
        Schema::dropIfExists('tilok_documents');
    }
};
