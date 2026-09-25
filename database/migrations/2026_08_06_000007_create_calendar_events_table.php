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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('task_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->string('color', 9)->default('#6366f1');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
            $table->index(['user_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
