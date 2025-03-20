<?php

use App\Enums\CourseTypeEnum;
use App\Enums\LevelEnum;
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
        Schema::create('formations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('level', LevelEnum::values())->default(LevelEnum::BEGINNER->value);
            $table->integer('duration')->unsigned(); // en semaine
            $table->integer('price');
            $table->integer('capacity')->unsigned();
            $table->integer('enrolled_students')->unsigned()->default(0); // Étudiants actuellement inscrits
            $table->foreignUuid('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('category_id')->constrained('categories')->restrictOnDelete();
            $table->enum('course_type', CourseTypeEnum::values())->default(CourseTypeEnum::DAY->value);
            $table->date('end_date');
            $table->date('start_date');
            $table->json('prerequisites')->nullable();
            $table->json('objectives')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
