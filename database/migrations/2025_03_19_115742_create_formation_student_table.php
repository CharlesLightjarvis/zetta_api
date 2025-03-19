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
        Schema::create('formation_student', function (Blueprint $table) {
            $table->uuid('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->uuid('student_id')->constrained('users')->cascadeOnDelete();

            $table->primary(['formation_id', 'student_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_student');
    }
};
