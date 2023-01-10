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
           $this->call('up');
        } catch (\Throwable $th) {
            $this->error("Something went wrong!. Try updating manually. If error persists feel free to open an issue \n \n Exception -> ".$th);
        }
    }

    private function intro()
    {
        $this->line("<bg=blue>Welcome to the Skuul update wizard</>");
        $this->warn("it's important to be connected to the internet,  always have a backup of both your codebase and your database before making updates. Review the release notes before updating, and test your system after updating to ensure everything is working correctly. If an issue arises, the community or dedicated support channels can provide help. Also, have a rollback plan in place.");
        // sleep(2);

        if (!$this->confirm('Do you wish to continue?')) {
            $this->error('Operation cancelled, thank you for using Skuul');
            exit();
        }
    }

    public function fetchLatestCode()
    {
        shell_exec('git fetch --all --tags && git checkout $(git rev-list --tags --max-count=1 ) && echo Updated codebase to  $(git rev-list --tags --max-count=1 )');
    }

    public function runUpdateCommands()
    {
        shell_exec('composer install');

        $this->call('migrate');
        $this->call('db:seed', ['class', 'RunInProductionSeeder']);
    }

}
