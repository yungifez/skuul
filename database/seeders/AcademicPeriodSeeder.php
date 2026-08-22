<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $academicPeriod = AcademicPeriod::firstOrCreate([
            'id' => 1, ], [
                'name'             => 'AcademicPeriod 1',
                'academic_year_id' => 1,
                'school_id'        => 1,
            ]);
        $academicPeriod->school->academic_period_id = $academicPeriod->id;
        $academicPeriod->school->save();
        AcademicPeriod::factory()->count(4)->create();
    }
}
