<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitCommand extends Command
{
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
    protected $description = 'Easily Istall Skuul';

    /**
     * No of attempts to be made to connect to the
     * Database, after which installation would stop.
     *
     * @var int
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
            $this->setDatabaseCredentials();
            $this->call('migrate', ['--force' => true]);
            $this->seedDatabase();
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
            $this->call('config:clear');
            $this->call('cache:clear');
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
            $this->call('key:generate');
        } else {
            $this->info('Encryption key exists already -- skipping');
        }
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
        $this->call('config:cache');
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
                $this->call('config:cache');
                DB::connection()->getPdo();

                $successfulConnection = true;
                //exit if connection could be made
                break;
            } catch (\Throwable $th) {
                $this->error("Couldn't connect with credentials. You would be prompted to enter/re-enter database credentails and connection would be retried. Not sure what these are?, you can reach out to your host's support or ask for help on github");
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
                $this->call('config:clear');
                $this->call('config:cache');
                $maxAttemptsRemaining--;
            }

            if ($maxAttemptsRemaining <= 0) {
                //stop execution if max attempts reached
                break;
            }
        } while (true);

        //if connection could not be made, max attempts were reached but could not connect to db
        if (false == $successfulConnection) {
            $this->error('Max db attempts exceecded please retry installation'.PHP_EOL);

            throw new Exception('Max db connections reached.');
        }

        $this->newLine();
        $this->line('Database set up successfully');
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
        $this->call('db:seed', ['--class' => 'WorldSeeder']);
        $this->clearCaches();
        $this->alert('Installation Completed');
    }

    public function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If environment variable does not exist, add it
                if ($keyPosition === false || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}=\"{$envValue}\"\n";
                } else {
                    //else replace it
                    $str = str_replace($oldLine, "{$envKey}=\"{$envValue}\"", $str);
                }
            }
        }

        $str = trim($str);
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }
}
