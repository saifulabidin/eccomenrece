<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\StoreConfig;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Share store config data to all views
        if (Schema::hasTable('store_configs')) {
            $storeConfig = StoreConfig::first();
            View::share('storeName', $storeConfig?->store_name ?? 'Katalog Online');
            View::share('storeWhatsapp', $storeConfig?->whatsapp_number ?? '6281234567890');
            View::share('storeAddress', $storeConfig?->address ?? '');
            View::share('storeDescription', $storeConfig?->description ?? '');
            View::share('heroImages', $storeConfig?->hero_images ?? []);
            View::share('storeLogo', $storeConfig?->logo ?? null);
        } else {
            View::share('storeName', 'Katalog Online');
            View::share('storeWhatsapp', '6281234567890');
            View::share('storeAddress', '');
            View::share('storeDescription', '');
            View::share('heroImages', []);
        }
    }
}
