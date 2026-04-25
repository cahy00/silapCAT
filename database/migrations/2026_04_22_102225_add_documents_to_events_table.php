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
        Schema::table('events', function (Blueprint $table) {
            $table->string('doc_implementation_report')->nullable(); // Laporan Pelaksanaan
            $table->string('doc_team_decree')->nullable();           // SK Tim
            $table->string('doc_ba_catos')->nullable();              // BA CATOS
            $table->string('doc_institution_announcement')->nullable(); // Pengumuman Instansi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'doc_implementation_report',
                'doc_team_decree',
                'doc_ba_catos',
                'doc_institution_announcement',
            ]);
        });
    }
};
