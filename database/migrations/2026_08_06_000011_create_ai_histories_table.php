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
        Schema::create('ai_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('feature', 40)->index();
            $table->longText('prompt');
            $table->longText('response')->nullable();
            $table->string('status', 16)->default('success')->index();
            $table->string('provider', 20)->nullable();
            $table->string('model', 60)->nullable();
            $table->unsignedInteger('tokens_in')->nullable();
            $table->unsignedInteger('tokens_out')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'feature', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_histories');
    }
};
