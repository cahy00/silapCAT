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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->foreignId('tilok_id')->references('id')->on('tiloks')->cascadeOnDelete();
            $table->string('instansi_name');
            $table->string('exam_date');
            $table->integer('session')->default(0);
            $table->integer('participant_total')->default(0);
            $table->integer('participant_present')->default(0);
            $table->integer('participant_absent')->default(0);
            $table->integer('highest_score')->default(0);
            $table->integer('lowest_score')->default(0);
            $table->integer('average_score')->default(0);
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
        Schema::dropIfExists('detail_tiloks');
    }
};
