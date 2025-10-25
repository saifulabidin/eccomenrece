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
        Schema::table('users', function (Blueprint $table) {
            // Change google_token and google_refresh_token to text for longer tokens
            $table->text('google_token')->nullable()->change();
            $table->text('google_refresh_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to string
            $table->string('google_token')->nullable()->change();
            $table->string('google_refresh_token')->nullable()->change();
        });
    }
};
