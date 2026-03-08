<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('exhibit_id')->nullable();
            $table->unsignedBigInteger('floor_plan_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->float('total_distance')->default(0);
            $table->json('destinations_visited')->nullable();
            $table->json('events')->nullable();
            $table->string('status')->default('active'); // active, completed, abandoned
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
            $table->index(['exhibit_id', 'started_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_sessions');
    }
};
