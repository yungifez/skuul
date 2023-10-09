<?php

namespace App\Console\Commands;

use App\Traits\EnvEditorTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InitCommand extends Command
{
    use EnvEditorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Skuul';

    /**
     * No of attempts to be made to connect to the
     * Database, after which installation would stop.
     */
    public int $maxDbConnectAttempts = 5;

    public string $env = 'production';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line("

        .----------------. .----------------. .----------------. .----------------. .----------------.
        | .--------------. | .--------------. | .--------------. | .--------------. | .--------------. |
        | |    _______   | | |  ___  ____   | | | _____  _____ | | | _____  _____ | | |   _____      | |
        | |   /  ___  |  | | | |_  ||_  _|  | | ||_   _||_   _|| | ||_   _||_   _|| | |  |_   _|     | |
        | |  |  (__ \_|  | | |   | |_/ /    | | |  | |    | |  | | |  | |    | |  | | |    | |       | |
        | |   '.___`-.   | | |   |  __'.    | | |  | '    ' |  | | |  | '    ' |  | | |    | |   _   | |
        | |  |`\____) |  | | |  _| |  \ \_  | | |   \ `--' /   | | |   \ `--' /   | | |   _| |__/ |  | |
        | |  |_______.'  | | | |____||____| | | |    `.__.'    | | |    `.__.'    | | |  |________|  | |
        | |              | | |              | | |              | | |              | | |              | |
        | '--------------' | '--------------' | '--------------' | '--------------' | '--------------' |
        '----------------' '----------------' '----------------' '----------------' '----------------'
        ");
        $this->info(
            'The installation would now begin.'
            .PHP_EOL
        );

        try {
            $this->clearCaches();
            $this->generateEnv();
            $this->generateAppKey();
            $this->setAppEnvironmentDetails();
            $this->buildNodeDependencies();
            $this->setDatabaseCredentials();
            $this->call('migrate', ['--force' => true]);
            $this->seedDatabase();
            $this->setMailCredentials();
            $this->createSuperAdmin();
            $this->finishingTouches();
        } catch (\Throwable $th) {
            $this->error("Something went wrong!. Try Installing manually. If error persists feel free to open an issue \n Exception -> ".$th);
        }

        return 0;
    }

    private function clearCaches(): void
    {
        $this->components->task('Clearing all caches', function (): void {
            Artisan::call('config:clear -q');
            Artisan::call('cache:clear -q');
        });
    }

    public function generateEnv()
    {
        $this->newLine();
        $this->line('Generating .env file.....');
        if (!file_exists(base_path('.env'))) {
            $this->components->task('Copying .env file', static function (): void {
                copy(base_path('.env.example'), base_path('.env'));
            });
        } else {
            $this->info('.env file exists -- skipping');
        }
    }

    public function generateAppKey()
    {
        $this->newLine();
        $this->line('Generating app encryption key');

        $key = $this->laravel['config']['app.key'];
        if (!$key) {
            Artisan::call('key:generate');
        } else {
            $this->info('Encryption key exists already -- skipping');
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

    public function setAppEnvironmentDetails()
    {
        $this->line("Set up your app's environment details");
        $this->env = $this->choice(
            'What environment are you installing skuul on?',
            ['local', 'production'],
            $this->env
        );

        switch ($this->env) {
            case 'local':
                $this->line('Setting environment as local');
                $appEnv = ['APP_DEBUG' => 'true', 'APP_ENV' => 'local'];
                break;
            case 'production':
                $appEnv = ['APP_DEBUG' => 'false', 'APP_ENV' => 'production'];
                break;
            default:
                $appEnv = ['APP_DEBUG' => 'false', 'APP_ENV' => 'production'];
                break;
        }
        $this->setEnvironmentValue($appEnv);
        $this->line('Environment set successfully');

        $this->newLine(2);
        $this->info('Please fill in the following details that would be used throughout the application, the input in the parenthesis would be used if left blank');

        $this->clearCaches();
        Artisan::call('config:cache -q');
        $appName = $this->ask('Application name', getenv('APP_NAME'));
        $appURL = $this->ask('Application URL', getenv('APP_URL'));

        $appDetails = ['APP_NAME' => $appName, 'APP_URL' => $appURL];
        $this->setEnvironmentValue($appDetails);
    }

    public function setDatabaseCredentials()
    {
        $this->newLine();
        $this->line('Setting up database');
        $successfulConnection = false;
        $maxAttemptsRemaining = $this->maxDbConnectAttempts;
        do {
            try {
                $this->clearCaches();
                Artisan::call('config:cache -q');
                DB::connection()->getPdo();

                $successfulConnection = true;
                //exit if connection could be made
                break;
            } catch (\Throwable $th) {
                $this->error("Couldn't connect with credentials. You would be prompted to enter/re-enter database credentials and connection would be retried. Not sure what these are?, you can reach out to your host's support or ask for help on github");
                $this->newLine();
                $this->line('Database details');
                $this->info('Attributes in parentheses are the default');
                $dbHost = $this->ask('Database Host ', getenv('DB_HOST'));
                $dbPort = $this->ask('Database Port ', getenv('DB_PORT'));
                $dbDatabase = $this->ask('Database Name', getenv('DB_DATABASE'));
                $dbUsername = $this->ask('Database Username', getenv('DB_USERNAME'));
                $dbPassword = $this->secret('Database Password', getenv('DB_PASSWORD'));

                $this->info('Attempting to connect');
                $dbData = ['DB_HOST' => $dbHost, 'DB_PORT' => $dbPort, 'DB_DATABASE' => $dbDatabase, 'DB_USERNAME' => $dbUsername, 'DB_PASSWORD' => $dbPassword];
                $this->setEnvironmentValue($dbData);
                // Set the config so that the next DB attempt uses refreshed credentials
                $this->clearCaches();
                Artisan::call('config:cache -q');

                $maxAttemptsRemaining--;
            }

            if ($maxAttemptsRemaining <= 0) {
                //stop execution if max attempts reached
                break;
            }
        } while (true);

        //if connection could not be made, max attempts were reached but could not connect to db
        if (false == $successfulConnection) {
            $this->error('Max db attempts exceeded please retry installation'.PHP_EOL);

            throw new \Exception('Max db connections reached.');
        }

        $this->newLine();
        $this->line('Database set up successfully');
    }

    public function setMailCredentials()
    {
        $confirm = $this->confirm('Do you want to set your mail credentials now?');
        if ($confirm == false) {
            return;
        }

        $mailMailer = $this->ask('Mail Mailer', getenv('MAIL_MAILER'));
        $mailHost = $this->ask('Mail Host', getenv('MAIL_HOST'));
        $mailPort = $this->ask('Mail Port', getenv('MAIL_PORT'));
        $mailUsername = $this->ask('Mail Username', getenv('MAIL_USERNAME'));
        $mailPassword = $this->ask('Mail Password', getenv('MAIL_PASSWORD'));
        $mailFromAddress = $this->ask('Mail From Address', getenv('MAIL_FROM_ADDRESS'));
        $mailFromName = $this->ask('Mail From Name', getenv('MAIL_FROM_NAME'));
        $mailReplyAddress = $this->ask('Mail Reply Address', getenv('MAIL_REPLY_ADDRESS'));
        $mailReplyName = $this->ask('Mail Reply Name', getenv('MAIL_REPLY_NAME'));

        $mailCredentials = [
            'MAIL_MAILER'        => $mailMailer,
            'MAIL_HOST'          => $mailHost,
            'MAIL_PORT'          => $mailPort,
            'MAIL_USERNAME'      => $mailUsername,
            'MAIL_PASSWORD'      => $mailPassword,
            'MAIL_FROM_ADDRESS'  => $mailFromAddress,
            'MAIL_FROM_NAME'     => $mailFromName,
            'MAIL_REPLY_ADDRESS' => $mailReplyAddress,
            'MAIL_REPLY_NAME'    => $mailReplyName,
        ];

        $this->setEnvironmentValue($mailCredentials);
    }

    public function seedDatabase()
    {
        switch ($this->env) {
            case 'local':
                $this->call('db:seed');
                break;
            case 'production':
                $this->call('db:seed', ['--class' => 'RunInProductionSeeder']);
                break;
            default:
                $this->call('db:seed', ['--class' => 'RunInProductionSeeder']);
                break;
        }
    }

    public function createSuperAdmin()
    {
        $this->newLine();
        $this->line('Creating super admin account');
        switch ($this->env) {
            case 'local':
                $this->info('Since you are trying out skuul locally, we have already created a super admin account for you. Check the docs on what these credentials are if unsure');
                break;
            default:
                $this->info('If you choose to not create a super admin now for some reason, you cen do so late by running php artisan skuul:create-super-admin');
                if ($this->confirm('Do you wish to create a super admin account now?', true)) {
                    $this->call('skuul:create-super-admin');
                } else {
                    $this->line('skipping...');
                }
                break;
        }
    }

    public function finishingTouches()
    {
        $this->line('Now to perform some finishing touches, this process might take a long time because the application is adding essential data into the application');
        $this->call('storage:link');
        $this->call('db:seed', ['--class' => 'WorldSeeder']);
        $this->clearCaches();
        $this->alert('Installation Completed');
    }
}
