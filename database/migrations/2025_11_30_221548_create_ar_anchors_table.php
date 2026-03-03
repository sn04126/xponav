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
        Schema::create('ar_anchors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_plan_id')->constrained('exhibit_floor_plans')->onDelete('cascade');
            $table->foreignId('exhibit_id')->nullable()->constrained('exhibits')->onDelete('set null');
            
            // Anchor identification
            $table->string('anchor_name'); // Unique name for the anchor
            $table->string('anchor_type'); // 'reference_point', 'exhibit_location', 'navigation_point', 'entrance', 'exit'
            $table->text('description')->nullable();
            
            // 3D Position (relative to floor plan origin in meters)
            $table->decimal('position_x', 10, 4); // X coordinate
            $table->decimal('position_y', 10, 4); // Y coordinate (height)
            $table->decimal('position_z', 10, 4); // Z coordinate
            
            // Rotation (quaternion for ARKit)
            $table->decimal('rotation_x', 10, 6)->default(0);
            $table->decimal('rotation_y', 10, 6)->default(0);
            $table->decimal('rotation_z', 10, 6)->default(0);
            $table->decimal('rotation_w', 10, 6)->default(1);
            
            // Alternative: Euler angles (degrees)
            $table->decimal('euler_x', 10, 4)->nullable(); // Pitch
            $table->decimal('euler_y', 10, 4)->nullable(); // Yaw
            $table->decimal('euler_z', 10, 4)->nullable(); // Roll
            
            // ARKit specific data
            $table->string('ar_anchor_identifier')->nullable(); // UUID from ARKit
            $table->json('ar_world_map_data')->nullable(); // Serialized ARWorldMap data
            
            // Visual marker (for initial anchor detection)
            $table->string('marker_image_path')->nullable(); // QR code or image marker
            $table->string('marker_type')->nullable(); // 'qr_code', 'image', 'nfc'
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional custom data
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For sorting/importance
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['floor_plan_id', 'anchor_type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_anchors');
    }
};
