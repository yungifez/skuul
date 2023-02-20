<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeeCategory::factory()->count(5)->create();
    }
}
