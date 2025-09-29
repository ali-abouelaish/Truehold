<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\RentalCode;
use App\Observers\RentalCodeObserver;

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
        // Add custom Blade directive for safe property access
        \Blade::directive('safe', function ($expression) {
            return "<?php echo is_string($expression) ? $expression : 'N/A'; ?>";
        });
        
        // Register model observers
        RentalCode::observe(RentalCodeObserver::class);
    }
}
