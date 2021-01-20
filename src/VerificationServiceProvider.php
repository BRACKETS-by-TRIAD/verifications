<?php


namespace Brackets\Verifications;

use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\EmailProvider;
use Brackets\Verifications\Channels\TwilioProvider;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\CodeGenerator\Contracts\GeneratorInterface;
use Brackets\Verifications\CodeGenerator\SimpleGenerator;
use Brackets\Verifications\Commands\AddLoginVerifyAttributeCommand;
use Brackets\Verifications\Commands\AddEmailAttributeCommand;
use Brackets\Verifications\Commands\AddPhoneAttributeCommand;
use Brackets\Verifications\Commands\VerificationsInstall;
use Brackets\Verifications\Middleware\VerifyMiddleware;
use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class VerificationServiceProvider extends ServiceProvider
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
            AddLoginVerifyAttributeCommand::class,
            AddEmailAttributeCommand::class,
            AddPhoneAttributeCommand::class
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'brackets/verifications');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'brackets/verifications');

        if (class_exists("Dotunj\\LaraTwilio\\LaraTwilioServiceProvider")) {
            $this->app->register("Dotunj\\LaraTwilio\\LaraTwilioServiceProvider");
        }

        $this->publishes([
            __DIR__ . '/Database/migrations/' => Container::getInstance()->databasePath('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'../resources/views' => Container::getInstance()->resourcePath('views/vendor/brackets/verifications'),
        ], 'views');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/verification-routes.php');
        $this->app->make(Router::class)->aliasMiddleware('verifications.verify', VerifyMiddleware::class);

        if (Config::get('verifications.enabled')) {
            $this->bindProviders();
        }
    }

    private function bindProviders()
    {
        $channelsCollection = collect(array_values(Config::get('verifications.actions')))->pluck('channel')->unique();

        $this->app->bind('verification', function() {
            return new Verification();
        });

        $this->app->bind(GeneratorInterface::class, SimpleGenerator::class);

        if ($channelsCollection->contains('sms')) {
            $this->app->bind(SMSProviderInterface::class, TwilioProvider::class);
        }

        if ($channelsCollection->contains('email')) {
            $this->app->bind(EmailProviderInterface::class, EmailProvider::class);
        }
    }
}
