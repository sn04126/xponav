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
        Schema::create('exhibit_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('exhibit_id')->constrained()->onDelete('cascade');
            $table->integer('duration_seconds')->default(0);
            $table->string('source')->default('ar_navigation'); // ar_navigation, manual, qr_scan
            $table->json('path_data')->nullable(); // Store the path taken
            $table->string('start_anchor')->nullable();
            $table->string('end_anchor')->nullable();
            $table->integer('floor_level')->nullable();
            $table->date('visit_date');
            $table->time('visit_time');
            $table->timestamps();

            // Indexes for analytics queries
            $table->index(['exhibit_id', 'visit_date']);
            $table->index(['user_id', 'visit_date']);
            $table->index('visit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibit_visits');
    }
};
