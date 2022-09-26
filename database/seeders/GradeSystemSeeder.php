<?php

namespace Database\Seeders;

use App\Models\GradeSystem;
use Illuminate\Database\Seeder;

class GradeSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GradeSystem::firstOrCreate([
            'id'             => 1,
            'name'           => 'A+',
            'remark'         => 'Excellent',
            'grade_from'     => '90',
            'grade_till'     => '100',
            'class_group_id' => 1,
        ]);

        GradeSystem::firstOrCreate([
            'id'             => 2,
            'name'           => 'A',
            'remark'         => 'Very Good',
            'grade_from'     => '80',
            'grade_till'     => '89',
            'class_group_id' => 1,
        ]);

        GradeSystem::firstOrCreate([
            'id'             => 3,
            'name'           => 'B+',
            'remark'         => 'Good',
            'grade_from'     => '70',
            'grade_till'     => '79',
            'class_group_id' => 1,
        ]);

        GradeSystem::firstOrCreate([
            'id'             => 4,
            'name'           => 'B',
            'remark'         => 'Satisfactory',
            'grade_from'     => '60',
            'grade_till'     => '69',
            'class_group_id' => 1,
        ]);

        GradeSystem::firstOrCreate([
            'id'             => 5,
            'name'           => 'C+',
            'remark'         => 'Fair',
            'grade_from'     => '50',
            'grade_till'     => '59',
            'class_group_id' => 1,
        ]);

        GradeSystem::firstOrCreate([
            'id'             => 6,
            'name'           => 'C',
            'remark'         => 'Pass',
            'grade_from'     => '40',
            'grade_till'     => '49',
            'class_group_id' => 1,
        ]);

        GradeSystem::firstOrCreate([
            'id'             => 7,
            'name'           => 'D',
            'remark'         => 'Fail',
            'grade_from'     => '20',
            'grade_till'     => '39',
            'class_group_id' => 1,
        ]);
    }
}
