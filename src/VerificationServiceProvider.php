<?php


namespace Brackets\Verifications;


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
    }
}
