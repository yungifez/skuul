<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::firstOrCreate([
            'id' => 1,
        ], [
            'name'        => 'Maths',
            'short_name'  => 'mat',
            'my_class_id' => 1,
            'school_id'   => 1,
        ]);

        Subject::firstOrCreate([
            'name'        => 'English',
            'short_name'  => 'eng',
            'my_class_id' => 2,
            'school_id'   => 1,
        ]);
    }
}
