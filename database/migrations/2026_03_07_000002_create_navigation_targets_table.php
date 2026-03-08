<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exhibit_id');
            $table->unsignedBigInteger('floor_plan_id')->nullable();
            $table->string('name');
            $table->float('position_x')->default(0);
            $table->float('position_y')->default(0);
            $table->float('position_z')->default(0);
            $table->float('rotation_y')->default(0);
            $table->string('category')->nullable(); // lobby, room, elevator, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['exhibit_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_targets');
    }
};
