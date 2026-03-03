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
        Schema::create('exhibits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('artist_name')->nullable();
            $table->text('artist_bio')->nullable();
            $table->string('category')->nullable();
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->decimal('ticket_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibits');
    }
};
