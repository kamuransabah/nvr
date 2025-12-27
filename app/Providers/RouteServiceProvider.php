<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {
            // Web rotalarını yükle
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Admin rotalarını yükle
            Route::middleware(['web', 'auth', 'role:admin'])
                ->prefix(config('system.admin_prefix'))
                ->name(config('system.admin_prefix').'.')
                ->group(base_path('routes/crm.php'));
        });
    }
}
