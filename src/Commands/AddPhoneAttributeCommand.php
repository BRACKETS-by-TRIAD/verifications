<?php

namespace Brackets\Verifications\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AddPhoneAttributeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verifications:add-phone {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds phone_number column to specified table';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('Generating migration...');

        $table = $this->argument('table');

        $this->call('make:migration', ['name' => 'add_phone_number_to_'. $table .'_table']);

        $this->info('Modifying migration...');

        $this->strReplaceInFile(
            $this->getMigrationFile('add_phone_number_to_'. $table .'_table'),
            $this->getReplaceToUp($table),
            $this->getGeneratedUp($table)
        );

        $this->strReplaceInFile(
            $this->getMigrationFile('add_phone_number_to_'. $table .'_table'),
            $this->getReplaceToDown($table),
            $this->getGeneratedDown($table)
        );

        $this->call('migrate');

        $this->info('Phone attribute has been successfully generated');
    }

    private function getMigrationFile($migrationSuffix)
    {
        $migrations = File::files(Container::getInstance()->databasePath('migrations/'));

        foreach ($migrations as $migration) {
            if (Str::contains($migration->getFilename(), $migrationSuffix)) {
                return $migration->getPath() . DIRECTORY_SEPARATOR . $migration->getFilename();
            }
        }

        return null;
    }

    private function strReplaceInFile($fileName, $toReplace, $replaceWith)
    {
        $content = File::get($fileName);

        return File::put($fileName, str_replace($toReplace, $replaceWith, $content));
    }

    private function getReplaceToUp(string $table)
    {
        return 'up()
    {
        Schema::table(\''. $table .'\', function (Blueprint $table) {
            //';
    }

    private function getGeneratedUp(string $table): string
    {
        return 'up()
    {
        Schema::table(\''. $table .'\', function (Blueprint $table) {
            $table->string(\'phone_number\')->nullable();';
    }

    private function getReplaceToDown(string $table)
    {
        return 'down()
    {
        Schema::table(\''. $table .'\', function (Blueprint $table) {
            //';
    }

    private function getGeneratedDown(string $table): string
    {
        return 'down()
    {
        Schema::table(\''. $table .'\', function (Blueprint $table) {
            $table->dropColumn([\'phone_number\']);';
    }
}
