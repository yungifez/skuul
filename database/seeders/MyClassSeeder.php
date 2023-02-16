<?php

namespace Database\Seeders;

use App\Models\MyClass;
use Illuminate\Database\Seeder;

class MyClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MyClass::firstOrcreate([
            'id'             => 1,
            'name'           => 'Kindergarten 1',
            'class_group_id' => 1,
        ]);

        MyClass::firstOrcreate([
            'id'             => 2,
            'name'           => 'Kindergarten 2',
            'class_group_id' => 1,
        ]);

        MyClass::firstOrcreate([
            'id'             => 3,
            'name'           => 'Nursery 1',
            'class_group_id' => 2,
        ]);

        MyClass::firstOrcreate([
            'id'             => 4,
            'name'           => 'Nursery 2',
            'class_group_id' => 2,
        ]);

        MyClass::firstOrcreate([
            'id'             => 5,
            'name'           => 'Primary 1',
            'class_group_id' => 3,
        ]);

        MyClass::firstOrcreate([
            'id'             => 7,
            'name'           => 'Primary 2',
            'class_group_id' => 4,
        ]);
        MyClass::factory()->count(5)->create();
    }
}
