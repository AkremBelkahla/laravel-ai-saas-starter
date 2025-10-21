<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('type'); // text, image
            $table->integer('delta'); // positive for credit, negative for debit
            $table->string('reason'); // monthly_reset, text_generation, image_generation, etc.
            $table->json('meta')->nullable(); // additional metadata
            $table->timestamps();

            $table->index(['team_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_ledgers');
    }
};
