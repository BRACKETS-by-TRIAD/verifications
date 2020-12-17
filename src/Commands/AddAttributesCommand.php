<?php


namespace Brackets\Verifications\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AddAttributesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verifications:add-attributes {table}';     //    "boolean('login_verify')->default(0)|string('phone_number')->nullable()" users

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
        $this->info('Generating attributes migration');

        $table = $this->argument('table');

        $this->call('make:migration', ['name' => 'add_attributes_to_'. $table .'_table']);

        $this->info('Modifying migration...');

        $this->strReplaceInFile(
            $this->getMigrationFile('add_attributes_to_'. $table .'_table'),
            $this->getReplaceToUp(),
            $this->getGeneratedUp()
        );

        $this->strReplaceInFile(
            $this->getMigrationFile('add_attributes_to_'. $table .'_table'),
            $this->getReplaceToDown(),
            $this->getAutoGeneratedDown()
        );

        $this->call('migrate');

        $this->info('Verification attributes has been successfully generated');
    }

    private function getMigrationFile($migrationSuffix)
    {
        $migrations = File::files(database_path('migrations/'));

        foreach($migrations as $migration) {
            if(Str::contains($migration->getFilename(), $migrationSuffix)) {
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

    private function getReplaceToUp()
    {
        return 'up()
    {
        Schema::table(\'users\', function (Blueprint $table) {
            //';
    }

    private function getGeneratedUp(): string
    {
        return 'up()
    {
        Schema::table(\'users\', function (Blueprint $table) {
            $table->boolean(\'login_verify_enabled\')->default(false);
            $table->string(\'phone_number\')->nullable();';
    }

    private function getReplaceToDown()
    {
        return 'down()
    {
        Schema::table(\'users\', function (Blueprint $table) {
            //';
    }

    private function getAutoGeneratedDown(): string
    {
        return 'down()
    {
        Schema::table(\'users\', function (Blueprint $table) {
            $table->dropColumn([\'login_verify_enabled\', \'phone_number\']);';
    }
}
