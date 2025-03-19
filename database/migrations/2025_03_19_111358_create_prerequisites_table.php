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
        Schema::create('prerequisites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('formation_id')->nullable()->constrained('formations')->cascadeOnDelete();  // Lien vers la formation (facultatif)
            $table->foreignUuid('certification_id')->nullable()->constrained('certifications')->cascadeOnDelete();  // Lien vers la certification (facultatif)
            $table->text('description')->nullable();
            $table->integer('order')->unsigned()->nullable();

            $table->unique(['formation_id', 'order']);
            $table->unique(['certification_id', 'order']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prerequisites');
    }
};
