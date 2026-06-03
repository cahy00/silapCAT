<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_surveys', function (Blueprint $table) {
            $table->date('survey_start_date')->nullable()->after('surveyor_name');
            $table->date('survey_end_date')->nullable()->after('survey_start_date');
            $table->dropColumn('survey_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_surveys', function (Blueprint $table) {
            $table->date('survey_date')->nullable()->after('surveyor_name');
            $table->dropColumn(['survey_start_date', 'survey_end_date']);
        });
    }
};
