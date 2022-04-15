<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RunInProductionSeeder::class,
            SchoolSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            ClassGroupSeeder::class,
            MyClassSeeder::class,
            SectionSeeder::class,
            UserSeeder::class,
            SubjectSeeder::class,
        ]);
    }
}
