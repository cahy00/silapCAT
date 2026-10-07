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
            $table->text('technical_issues')->nullable()->after('doc_institution_announcement');
            $table->text('executive_notes')->nullable()->after('technical_issues');
            $table->json('documentation_photos')->nullable()->after('executive_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'technical_issues',
                'executive_notes',
                'documentation_photos',
            ]);
        });
    }
};
