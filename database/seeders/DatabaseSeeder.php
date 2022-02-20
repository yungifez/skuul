<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\MyClassSeeder;
use Database\Seeders\SectionSeeder;
use Database\Seeders\RunInProductionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([RunInProductionSeeder::class,
            SchoolSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            ClassGroupSeeder::class,
            MyClassSeeder::class,
            SectionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
