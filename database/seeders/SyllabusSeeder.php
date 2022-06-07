<?php

namespace Database\Seeders;

use App\Models\Syllabus;
use Illuminate\Database\Seeder;

class SyllabusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Syllabus::factory()->count(10)->create();
    }
}
