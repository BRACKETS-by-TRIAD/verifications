<?php


namespace Brackets\Verifications;


use Brackets\Verifications\Channels\EmailProviderInterface;
use Brackets\Verifications\Channels\EmailProvider;
use Brackets\Verifications\Channels\SMSProvider;
use Brackets\Verifications\Channels\SMSProviderInterface;
use Brackets\Verifications\Commands\VerificationsInstall;
use Illuminate\Support\ServiceProvider;
use Twilio\TwiML\Voice\Sms;

class VerificationServiceProvider  extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            VerificationsInstall::class,
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'brackets/verifications');

        if(class_exists("Dotunj\\LaraTwilio\\LaraTwilioServiceProvider")) {
            $this->app->register("Dotunj\\LaraTwilio\\LaraTwilioServiceProvider");
        }

        $this->publishes([
            __DIR__ . '/Database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/verifications-routes.php');

        if(config('verifications.enabled')) {
            $this->bindProviders();
        }
    }

    private function bindProviders()
    {
        $entities = array_merge(config('verifications.default'), config('verifications.2fa'));

        $channels = array_values($entities)['channel'];

        if(in_array('sms', $channels)) {
            $this->app->bind(SMSProviderInterface::class, function() {
                return new SMSProvider();
            });
        }

        if(in_array('email', $channels)) {
            $this->app->bind(EmailProviderInterface::class, function () {
                return new EmailProvider();
            });
        }
    }
}
