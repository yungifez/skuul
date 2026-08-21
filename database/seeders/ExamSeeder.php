<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Exam::firstOrCreate([
            'id'          => 1,
            'name'        => 'Exam 1',
            'description' => 'Exam 1 description',
            'semester_id' => '1',
            'start_date'  => '2020-01-01',
            'stop_date'   => '2020-01-31',
        ]);

        Exam::factory()->count(10)->create();
    }
}
