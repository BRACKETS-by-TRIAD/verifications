<?php

namespace Brackets\Verifications\Commands;


use Brackets\Verifications\VerificationServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;

class VerificationsInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verifications:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a brackets/verifications package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('Installing package brackets/verifications');

        $this->publishConfig();

        $this->call('vendor:publish', [
            '--provider' => VerificationServiceProvider::class,
        ]);

        $this->call('migrate');

        $this->info('Package brackets/verifications installed');
    }

    private function publishConfig()
    {
        if (!File::exists(Container::getInstance()->configPath('verifications.php'))) {
            File::copy(__DIR__ . '/../../config/verifications.php', Container::getInstance()->configPath('verifications.php'));
        }

        if (!File::exists(Container::getInstance()->configPath('twilio.php'))) {
            File::copy(__DIR__ . '/../../config/twilio.php', Container::getInstance()->configPath('twilio.php'));
        }
    }
}
