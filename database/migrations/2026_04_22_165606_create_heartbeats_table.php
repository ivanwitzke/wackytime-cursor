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
        Schema::create('heartbeats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('heartbeat_at', 16, 6);
            $table->dateTime('occurred_at', precision: 6);
            $table->string('entity', 2048);
            $table->string('type', 20);
            $table->string('project_name')->nullable();
            $table->string('language', 120)->nullable();
            $table->string('editor', 120)->nullable();
            $table->boolean('is_write')->default(false);
            $table->char('dedupe_hash', 64)->unique();
            $table->timestamps();

            $table->index(['user_id', 'occurred_at']);
            $table->index(['user_id', 'project_name', 'occurred_at']);
            $table->index(['user_id', 'language', 'occurred_at']);
            $table->index(['user_id', 'editor', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heartbeats');
    }
};
