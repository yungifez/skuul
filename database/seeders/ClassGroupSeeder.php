<?php

namespace Database\Seeders;

use App\Models\ClassGroup;
use Illuminate\Database\Seeder;

class ClassGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClassGroup::firstOrcreate([
            'id'        => 1,
            'name'      => 'Kindergarten',
            'school_id' => 1,
        ]);
        ClassGroup::firstOrcreate([
            'id'        => 2,
            'name'      => 'Nursery',
            'school_id' => 1,
        ]);
        ClassGroup::firstOrcreate([
            'id'        => 3,
            'name'      => 'Primary',
            'school_id' => 1,
        ]);
        ClassGroup::firstOrcreate([
            'id'        => 4,
            'name'      => 'Secondary',
            'school_id' => 1,
        ]);
        ClassGroup::factory()->times(4)->create();
    }
}
