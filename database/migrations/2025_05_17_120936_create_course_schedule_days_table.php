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
        Schema::create('course_schedule_days', function (Blueprint $table) {
             $table->uuid('id')->primary();
            $table->foreignUuid('course_schedule_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 1 = Lundi, 7 = Dimanche
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_schedule_days');
    }
};
