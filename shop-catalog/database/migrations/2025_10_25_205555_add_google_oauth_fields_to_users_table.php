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
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('google_name')->nullable()->after('name');
            $table->string('google_avatar')->nullable()->after('email');
            $table->string('google_token')->nullable()->after('google_avatar');
            $table->string('google_refresh_token')->nullable()->after('google_token');
            $table->boolean('is_admin')->default(false)->after('google_refresh_token');
            $table->timestamp('google_expires_in')->nullable()->after('is_admin');

            // Add indexes
            $table->index('google_id');
            $table->index('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['google_id']);
            $table->dropIndex(['is_admin']);
            $table->dropColumn([
                'google_id',
                'google_name',
                'google_avatar',
                'google_token',
                'google_refresh_token',
                'is_admin',
                'google_expires_in'
            ]);
        });
    }
};
