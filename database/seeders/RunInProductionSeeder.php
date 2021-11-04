<?php

namespace Database\Seeders;

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
        $this->call(RoleSeeder::class);
    }
}
