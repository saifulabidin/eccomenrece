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
        Schema::table('store_configs', function (Blueprint $table) {
            $table->string('favicon')->nullable()->after('logo');
            $table->string('pwa_icon_192')->nullable()->after('favicon');
            $table->string('pwa_icon_512')->nullable()->after('pwa_icon_192');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_configs', function (Blueprint $table) {
            $table->dropColumn(['favicon', 'pwa_icon_192', 'pwa_icon_512']);
        });
    }
};
