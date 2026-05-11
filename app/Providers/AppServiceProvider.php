<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Wallet;
use App\Services\Payments\GatewayManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GatewayManager::class, function ($app) {
            return new GatewayManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        User::created(function (User $user) {
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'auto_settle_to_bank' => true,
                    'default_bank_id' => null,
                ]
            );
        });
    }
}
