<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('from_status', ['open', 'in_progress', 'on_hold', 'resolved', 'closed'])->nullable();
            $table->enum('to_status', ['open', 'in_progress', 'on_hold', 'resolved', 'closed']);
            $table->string('note', 1000)->nullable();
            $table->timestamp('changed_at');
            $table->timestamps(); 

            $table->index(['issue_id', 'changed_at']);
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_status_changes');
    }
};