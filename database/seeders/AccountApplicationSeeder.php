<?php

namespace Database\Seeders;

use App\Models\AccountApplication;
use Illuminate\Database\Seeder;

class AccountApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccountApplication::factory()->count(10)->create();
    }
}
