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
        Schema::create('exhibit_floor_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibit_id')->constrained('exhibits')->onDelete('cascade');
            $table->string('name'); // Floor plan name (e.g., "Ground Floor", "First Floor")
            $table->text('description')->nullable();
            
            // 3D Model files
            $table->string('model_file_path'); // Path to 3D model file (.usdz for ARKit)
            $table->string('thumbnail_path')->nullable(); // 2D preview image
            
            // Floor dimensions (in meters)
            $table->decimal('width', 10, 2); // Width in meters
            $table->decimal('height', 10, 2); // Height in meters
            $table->decimal('length', 10, 2); // Length in meters
            
            // Floor level
            $table->integer('floor_level')->default(0); // 0 = ground, 1 = first floor, etc.
            
            // AR World Origin Point (reference point for AR coordinates)
            $table->decimal('origin_latitude', 10, 8)->nullable();
            $table->decimal('origin_longitude', 11, 8)->nullable();
            $table->decimal('origin_altitude', 10, 2)->nullable();
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional data like scale, rotation, etc.
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibit_floor_plans');
    }
};
