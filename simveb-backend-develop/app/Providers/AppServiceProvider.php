<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Services\SmsService;
use FedaPay\FedaPay;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
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
        App::setLocale(config('app.locale'));

        /* Replace YOUR_API_SECRET_KEY by your API secret key */
        FedaPay::setApiKey(config('app.fedapay_sk'));
        /* Specify whenever you are willing to execute your request in test or live mode */
        FedaPay::setEnvironment(config('app.fedapay_env')); //or setEnvironment('live');

        Notification::extend('sms', function ($app) {
            return new SmsChannel($app->make(SmsService::class));
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $frontendUrl = 'http://cool-app.com/auth/email/verify';

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
                absolute: false
            );

            return $frontendUrl . '?verify_url=' . urlencode($verifyUrl);
        });

        if (app()->env != 'local') {
            \URL::forceScheme('https');
        }
    }
}
