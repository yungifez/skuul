<?php

namespace App\Console\Commands;

use App\Services\Demo\DemoDataRefresher;
use Illuminate\Console\Command;

/**
 * Reset the public demo to its seeded school.
 *
 * The reset removes every record, so it runs only where demo mode is on.
 * A real school never turns demo mode on, which keeps its data out of reach.
 */
class RefreshDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:refresh-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the demo school to its seeded data';

    /**
     * Execute the console command.
     */
    public function handle(DemoDataRefresher $refresher): int
    {
        if (!config('demo.enabled')) {
            $this->error('Demo mode is off. Set DEMO_MODE=true to reset this database to the demo school.');

            return self::FAILURE;
        }

        $refresher->refresh();

        $this->info('The demo school is reset.');

        return self::SUCCESS;
    }
}
