<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        // Custom Directives
        Blade::directive('isAdmin', function() {
            return "<?php if(auth()->check() && auth()->user()->is_admin): ?>";
        });
        Blade::directive('endIsAdmin', function() {
            return "<?php endif; ?>";
        });
    }
}
