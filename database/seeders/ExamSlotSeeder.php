<?php

namespace Database\Seeders;

use App\Models\ExamSlot;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            'name' => '1st CA',
            'description' => 'written last week',
            'total_marks' => 10,
            'exam_id' => 1
        ]);

        ExamSlot::factory()->count(10)->create();
    }
}
