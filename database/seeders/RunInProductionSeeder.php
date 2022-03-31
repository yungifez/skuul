<?php

namespace Database\Seeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\SchoolSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class RunInProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            WeekdaySeeder::class,
        ]);
    }
}
