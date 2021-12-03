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
        MyClass::create([
            'id' => 1,
            'name' => 'Kindergarten 1',
            'class_group_id' => 1,
        ]);

        MyClass::create([
            'id' => 2,
            'name' => 'Kindergarten 2',
            'class_group_id' => 1,
        ]);

        MyClass::create([
            'id' => 3,
            'name' => 'Nursery 1',
            'class_group_id' => 2,
        ]);

        
        MyClass::create([
            'id' => 4,
            'name' => 'Nursery 2',
            'class_group_id' => 2,
        ]);

        MyClass::create([
            'id' => 5,
            'name' => 'Primary 1',
            'class_group_id' => 3,
        ]);

        MyClass::create([
            'id' => 7,
            'name' => 'Primary 2',
            'class_group_id' => 4,
        ]);
    }
}
