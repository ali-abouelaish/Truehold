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
        // Ensure Google API Client classes are autoloaded
        if (class_exists('Composer\Autoload\ClassLoader')) {
            $loader = require base_path('vendor/autoload.php');
            // Force load Google API client namespace
            $loader->addPsr4('Google\\', base_path('vendor/google/apiclient/src'));
            $loader->addPsr4('Google\\Service\\', base_path('vendor/google/apiclient-services/src'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
        
        // Add custom Blade directive for safe property access
        \Blade::directive('safe', function ($expression) {
            return "<?php echo is_string($expression) ? $expression : 'N/A'; ?>";
        });
        
        // Register model observers
        RentalCode::observe(RentalCodeObserver::class);
    }
}
