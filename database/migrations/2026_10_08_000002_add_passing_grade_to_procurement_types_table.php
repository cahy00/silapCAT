<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_types', function (Blueprint $table) {
            $table->decimal('passing_grade', 8, 2)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('procurement_types', function (Blueprint $table) {
            $table->dropColumn('passing_grade');
        });
    }
};
