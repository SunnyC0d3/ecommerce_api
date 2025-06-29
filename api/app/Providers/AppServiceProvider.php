<?php

namespace App\Providers;

use App\Services\V1\Emails\Email;
use App\Services\V1\Logger\SecurityLog;
use App\Services\V1\Orders\Returns;
use App\Services\V1\Payments\StripePayment;
use App\Services\V1\Webhook\StripeWebhook;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void{}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return config('services.app_frontend_url') . config('services.app_frontend_pwr') . '?token=' . $token;
        });

        $this->app->singleton(SecurityLog::class, function ($app) {
            return new SecurityLog();
        });

        $this->app->singleton(Email::class);

        $this->app->bind(StripeWebhook::class, function ($app) {
            return new StripeWebhook(
                $app->make(Email::class)
            );
        });

        $this->app->bind(StripePayment::class, function ($app) {
            return new StripePayment(
                $app->make(Email::class)
            );
        });

        $this->app->bind(Returns::class, function ($app) {
            return new Returns(
                $app->make(Email::class)
            );
        });
    }
}
