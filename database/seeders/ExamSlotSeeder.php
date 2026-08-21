<?php

namespace Database\Seeders;

use App\Models\ExamSlot;
use Illuminate\Database\Seeder;

class ExamSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ExamSlot::firstOrCreate([
            'name'        => 'Objective',
            'description' => 'written last week',
            'total_marks' => 40,
            'exam_id'     => 1,
        ]);

        ExamSlot::firstOrCreate([
            'name'        => 'Theory',
            'description' => 'lol',
            'total_marks' => 60,
            'exam_id'     => 1,
        ]);

        ExamSlot::factory()->count(10)->create();
    }
}
