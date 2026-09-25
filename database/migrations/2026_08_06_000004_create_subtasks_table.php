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
        Schema::create('subtasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->string('title');
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
            $table->index(['task_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
