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
        Schema::create('reminders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('remindable_type');
            $table->string('remindable_id');
            $table->dateTime('remind_at')->index();
            $table->string('channel', 10)->default('app');
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_sent')->default(false)->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['remindable_type', 'remindable_id']);
            $table->index(['user_id', 'remind_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
