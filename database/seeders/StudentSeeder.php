<?php

namespace Database\Seeders;

use App\Models\StudentRecord;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StudentRecord::factory()->count(10)->create();
    }
}
