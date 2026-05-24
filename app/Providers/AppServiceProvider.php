<?php

namespace App\Providers;

use App\Listeners\SendOpsNotificationOnUserRegistered;
use App\Models\ApplicationLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WebsiteSetting;
use App\Observers\TransactionObserver;
use App\Contracts\SmsSender;
use App\Services\Logging\AppLogger;
use App\Services\Logging\FlowLog;
use App\Services\Payments\GatewayManager;
use App\Services\Sms\ApitxtSmsSender;
use App\Services\Sms\LogSmsSender;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

        $this->app->singleton(AppLogger::class);
        $this->app->singleton(FlowLog::class);

        $this->app->singleton(SmsSender::class, function ($app) {
            return config('sms.driver') === 'log'
                ? $app->make(LogSmsSender::class)
                : $app->make(ApitxtSmsSender::class);
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

        RateLimiter::for('otp-send', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perHour(30)->by($request->ip()),
            ];
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            return [
                Limit::perMinute(15)->by($request->ip()),
                Limit::perHour(60)->by($request->ip()),
            ];
        });

        Route::bind('log', fn (string $value) => ApplicationLog::query()->findOrFail($value));

        View::composer('*', function ($view) {
            $view->with('siteSettings', WebsiteSetting::cached());
        });

        Event::listen(Registered::class, SendOpsNotificationOnUserRegistered::class);

        Transaction::observe(TransactionObserver::class);

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
