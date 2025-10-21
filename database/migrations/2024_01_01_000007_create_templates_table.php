<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // text, image
            $table->text('prompt_template');
            $table->json('options')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['team_id', 'type']);
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
