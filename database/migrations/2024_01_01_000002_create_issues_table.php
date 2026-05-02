<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title', 255);
            $table->text('description');

            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('category', ['bug', 'feature', 'security', 'performance', 'infrastructure', 'data_issue', 'ux', 'other'])->default('other');
            $table->enum('status', ['open', 'in_progress', 'on_hold', 'resolved', 'closed'])->default('open');

            // AI-generated fields
            $table->text('ai_summary')->nullable();
            $table->string('ai_next_action', 500)->nullable();

            // Escalation
            $table->boolean('is_escalated')->default(false);
            $table->string('escalation_reason', 500)->nullable();

            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Indexes for common filter queries
            $table->index(['status', 'priority']);
            $table->index(['category', 'status']);
            $table->index(['is_escalated', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};