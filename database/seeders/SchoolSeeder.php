<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organization = Organization::firstOrCreate(
            ['code' => 'default-organization'],
            ['name' => 'Default Organization']
        );

        School::updateOrCreate(
            ['id' => 1],
            [
                'name'            => 'Default School',
                'organization_id' => $organization->id,
                'address'         => 'School of Computing',
                'code'            => Str::Random(10),
                'initials'        => 'DSI',
            ]
        );

        School::factory()
            ->count(3)
            ->create();
    }
}
