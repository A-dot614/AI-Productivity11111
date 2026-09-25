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
        Schema::create('productivity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('task_id')->nullable();
            $table->date('log_date')->index();
            $table->unsignedInteger('focus_minutes')->default(0);
            $table->unsignedInteger('completed_tasks')->default(0);
            $table->unsignedInteger('planned_tasks')->default(0);
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
            $table->unique(['user_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productivity_logs');
    }
};
