<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change provider_token from VARCHAR(255) to TEXT.
     *
     * OAuth access tokens (especially Google) can exceed 255 characters,
     * causing SQLSTATE[22001] truncation errors.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('provider_token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_token')->nullable()->change();
        });
    }
};
