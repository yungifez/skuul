<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schools')->delete();

        School::create([
            'id' => 1,
            'name' => 'Default School',
            'address' => 'School of Computing',
            'code' => Str::Random(10),
            'initials' => 'DSI',
        ]);

        School::factory()
                ->count(3)
                ->create();
    }
}
