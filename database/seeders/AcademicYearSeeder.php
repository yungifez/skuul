<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $academicYear = AcademicYear::firstOrCreate([
            'id' => 1,

        ], [
            'school_id'  => 1,
            'start_year' => date('Y'),
            'stop_year'  => date('Y') + 1,
        ]);
        $academicYear->school->academic_year_id = $academicYear->id;
        $academicYear->school->save();
        AcademicYear::factory()->count(4)->create();
    }
}
