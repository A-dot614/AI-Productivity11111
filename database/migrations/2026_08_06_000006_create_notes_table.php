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
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('task_id')->nullable();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->boolean('is_pinned')->default(false)->index();
            $table->string('color', 9)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
            $table->index(['user_id', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
