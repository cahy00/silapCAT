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
        Schema::create('exam_scores', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('institution')->nullable();
            $table->string('exam_type'); // 'UD_I', 'UD_II', 'UPKP'
            $table->date('exam_date');
            $table->decimal('cat_score', 5, 2);
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->string('status'); // 'Lulus', 'Tidak Lulus'
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexing for faster lookups
            $table->index('employee_number');
            $table->index('exam_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_scores');
    }
};
