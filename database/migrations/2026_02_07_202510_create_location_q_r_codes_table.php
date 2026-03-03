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
        Schema::create('location_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique QR code identifier
            $table->string('name'); // Human-readable name (e.g., "Main Entrance", "Hall A Entry")
            $table->text('description')->nullable();

            // Location reference
            $table->foreignId('exhibit_id')->constrained()->onDelete('cascade');
            $table->foreignId('floor_plan_id')->constrained('exhibit_floor_plans')->onDelete('cascade');

            // Position in the floor plan (model-local coordinates)
            $table->float('position_x')->default(0);
            $table->float('position_y')->default(0);
            $table->float('position_z')->default(0);

            // Rotation (for proper model orientation)
            $table->float('rotation_y')->default(0); // Degrees

            // Associated anchor (optional - can link to nearest anchor)
            $table->foreignId('anchor_id')->nullable()->constrained('ar_anchors')->onDelete('set null');

            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('scan_count')->default(0); // Track how many times scanned

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index(['exhibit_id', 'floor_plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_qr_codes');
    }
};
