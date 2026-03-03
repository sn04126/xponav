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
        Schema::create('exhibit_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exhibit_id')->constrained()->onDelete('cascade');
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_visited')->default(false);
            $table->timestamp('visited_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'exhibit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibit_user');
    }
};
