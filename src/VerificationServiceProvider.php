<?php


namespace Brackets\Verifications;


use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\EmailProvider;
use Brackets\Verifications\Channels\TwilioProvider;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\Commands\Add2faCommand;
use Brackets\Verifications\Commands\AddEmailCommand;
use Brackets\Verifications\Commands\AddPhoneCommand;
use Brackets\Verifications\Commands\VerificationsInstall;
use Illuminate\Support\ServiceProvider;

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
            Add2faCommand::class,
            AddEmailCommand::class,
            AddPhoneCommand::class
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'brackets/verifications');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'brackets/verifications');

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
            $this->app->singleton(Verification::class);
        }
    }

    private function bindProviders()
    {
        $allChannels = array_values(config('verifications.actions'))['channel'];
        $channels = array_unique($allChannels);

        if(in_array('sms', $channels)) {
            $this->app->bind(SMSProviderInterface::class, TwilioProvider::class);
        }

        if(in_array('email', $channels)) {
            $this->app->bind(EmailProviderInterface::class, EmailProvider::class);
        }
    }
}
