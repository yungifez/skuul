<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $semester = Semester::firstOrCreate([
            'id' => 1,
            'name' => 'Semester 1',
            'academic_year_id' => 1,
            'school_id' => 1,
        ]);
        $semester->school->semester_id = $semester->id;
        $semester->school->save();
        Semester::factory()->count(4)->create();
    }
}
