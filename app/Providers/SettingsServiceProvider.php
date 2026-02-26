<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Check if the settings table exists to prevent errors during initial setup/migrations.
        if (Schema::hasTable('settings')) {
            try {
                $settings = DB::table('settings')->get();

                foreach ($settings as $setting) {
                    // Use Config::set() to dynamically update the configuration.
                    // We'll prefix our settings with 'settings.' for clarity.
                    Config::set('settings.' . $setting->key, $setting->value);
                }

                // Specifically override the main app name config
                if ($appName = Config::get('settings.app_name')) {
                    Config::set('app.name', $appName);
                }
            } catch (\Exception $e) {
                // Fails silently if the database isn't ready, preventing crashes.
                return;
            }
        }
    }
}