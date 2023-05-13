<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update application to latest version';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->intro();
            $this->call('down');
            $this->call('optimize:clear');
            $this->fetchLatestCode();
            $this->runUpdateCommands();
            $this->buildNodeDependencies();
            $this->optimize();
            shell_exec('chmod 775 -R ./storage ./boostrap');
            $this->call('up');
        } catch (\Throwable $th) {
            $this->error("Something went wrong!. Try updating manually. If error persists feel free to open an issue \n \n Exception -> ".$th);
        }

        return 0;
    }

    private function intro()
    {
        $this->line('<bg=blue>Welcome to the Skuul update wizard</>');
        $this->warn("it's important to be connected to the internet,  always have a backup of both your codebase and your database before making updates. Review the release notes before updating, and test your system after updating to ensure everything is working correctly. If an issue arises, the community or dedicated support channels can provide help. Also, have a rollback plan in place.");

        sleep(2);

        //verify user is not root
        if (posix_getuid() == 0) {
            $this->error('This Command cannot be run by root user, thank you for using Skuul');
            exit;
        }

        if (!$this->confirm('Do you wish to continue?')) {
            $this->error('Operation cancelled, thank you for using Skuul');
            exit;
        }
    }

    public function fetchLatestCode()
    {
        $oldVersion = shell_exec('git reset --hard');
        $oldVersion = shell_exec('git describe --tags');

        $oldVersionAsArray = $this->splitVersionNumber($oldVersion);

        shell_exec('git fetch --all --tags && git checkout $(git rev-list --tags --max-count=1 )');

        $newVersion = shell_exec('git describe --tags $(git rev-list --tags --max-count=1)');

        $newVersionAsArray = $this->splitVersionNumber($newVersion);

        $this->info('Old Version: '.$oldVersion);
        $this->info('New Version: '.$newVersion);

        if ($oldVersionAsArray[0] != $newVersionAsArray[0]) {
            shell_exec('git checkout $(git rev-list --tags --max-count=1 )');
            $this->error('Update to major version is not allowed');

            exit;
        }
    }

    public function buildNodeDependencies()
    {
        $confirm = $this->confirm('Do you have node installed and want to build node dependencies?');

        if ($confirm == false) {
            return;
        }

        shell_exec('npm install');
        shell_exec('npm run build');
    }

    public function runUpdateCommands()
    {
        shell_exec('composer install');

        $this->call('migrate');

        $this->call('db:seed', ['--class' => 'RunInProductionSeeder']);
    }

    public function optimize()
    {
        if (!$this->confirm('Do you want to optimize this application?')) {
            return;
        }

        $this->call('optimize');
        $this->call('view:cache');
        $this->call('event:cache');
        shell_exec('composer install --optimize-autoloader ');
    }

    private function splitVersionNumber($versionNumber)
    {
        $versionNumber = preg_replace('/-.*/', '', $versionNumber);
        $versionNumber = preg_replace('/[a-zA-Z]/', '', $versionNumber);
        $versionNumber = str_replace(PHP_EOL, '', $versionNumber);
        $versionNumber = explode('.', $versionNumber);

        return $versionNumber;
    }
}
