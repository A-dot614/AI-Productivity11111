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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('priority', 10)->default('medium')->index();
            $table->dateTime('due_date')->nullable()->index();
            $table->dateTime('scheduled_at')->nullable()->index();
            $table->unsignedInteger('estimated_minutes')->nullable();
            $table->unsignedInteger('actual_minutes')->nullable();
            $table->string('recurrence', 16)->default('none');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('tasks')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->index(['user_id', 'status', 'due_date']);
            $table->index(['user_id', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
