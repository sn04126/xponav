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
        Schema::create('position_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('exhibit_id')->constrained()->onDelete('cascade');
            $table->foreignId('floor_plan_id')->constrained('exhibit_floor_plans')->onDelete('cascade');
            $table->decimal('position_x', 10, 4);
            $table->decimal('position_y', 10, 4);
            $table->decimal('position_z', 10, 4);
            $table->string('session_id', 64)->nullable(); // To group tracks from same navigation session
            $table->timestamp('tracked_at');
            $table->timestamps();

            // Indexes for efficient heat map queries
            $table->index(['exhibit_id', 'floor_plan_id', 'tracked_at']);
            $table->index(['position_x', 'position_z']); // For grid-based aggregation
        });

        // Create aggregated heat map data table for performance
        Schema::create('heat_map_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibit_id')->constrained()->onDelete('cascade');
            $table->foreignId('floor_plan_id')->constrained('exhibit_floor_plans')->onDelete('cascade');
            $table->decimal('grid_x', 10, 2); // Grid cell center X
            $table->decimal('grid_z', 10, 2); // Grid cell center Z
            $table->decimal('grid_size', 5, 2)->default(1.0); // Size of grid cell
            $table->integer('visit_count')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->date('aggregation_date');
            $table->timestamps();

            // Unique constraint for grid cells per day
            $table->unique(['exhibit_id', 'floor_plan_id', 'grid_x', 'grid_z', 'aggregation_date'], 'heat_map_unique_cell');

            // Index for fast lookups
            $table->index(['exhibit_id', 'floor_plan_id', 'aggregation_date'], 'heatmap_agg_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heat_map_aggregates');
        Schema::dropIfExists('position_tracks');
    }
};
