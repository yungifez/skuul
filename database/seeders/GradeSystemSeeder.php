<?php

namespace Database\Seeders;

use App\Models\GradeSystem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GradeSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GradeSystem::create([
            'id' => 1,
            'name' => 'A+',
            'remark' => 'Excellent',
            'grade_from' => '90',
            'grade_to' => '100',
            'class_group_id' => 1,
        ]);

        GradeSystem::create([
            'id' => 2,
            'name' => 'A',
            'remark' => 'Very Good',
            'grade_from' => '80',
            'grade_to' => '89',
            'class_group_id' => 1,
        ]);

        GradeSystem::create([
            'id' => 3,
            'name' => 'B+',
            'remark' => 'Good',
            'grade_from' => '70',
            'grade_to' => '79',
            'class_group_id' => 1,
        ]);

        GradeSystem::create([
            'id' => 4,
            'name' => 'B',
            'remark' => 'Satisfactory',
            'grade_from' => '60',
            'grade_to' => '69',
            'class_group_id' => 1,
        ]);

        GradeSystem::create([
            'id' => 5,
            'name' => 'C+',
            'remark' => 'Fair',
            'grade_from' => '50',
            'grade_to' => '59',
            'class_group_id' => 1,
        ]);

        GradeSystem::create([
            'id' => 6,
            'name' => 'C',
            'remark' => 'Pass',
            'grade_from' => '40',
            'grade_to' => '49',
            'class_group_id' => 1,
        ]);

        GradeSystem::create([
            'id' => 7,
            'name' => 'D',
            'remark' => 'Fail',
            'grade_from' => '20',
            'grade_to' => '39',
            'class_group_id' => 1,
        ]);
    }
}
