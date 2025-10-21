<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('type'); // text, image
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('prompt');
            $table->json('options')->nullable();
            $table->longText('result')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('estimated_cost')->nullable();
            $table->integer('actual_cost')->nullable();
            $table->string('model')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'type']);
            $table->index(['team_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_jobs');
    }
};
