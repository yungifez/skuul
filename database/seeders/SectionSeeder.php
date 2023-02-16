<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Section::firstOrCreate([
            'id'          => 1,
            'name'        => 'Gold',
            'my_class_id' => 1,
        ]);

        Section::firstOrCreate([
            'id'          => 2,
            'name'        => 'Ruby',
            'my_class_id' => 1,
        ]);

        Section::factory()->count(10)->create();
    }
}
