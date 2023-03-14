<?php

namespace Database\Seeders;

use App\Models\FeeInvoice;
use Illuminate\Database\Seeder;

class FeeInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeeInvoice::factory()->count(30)->create();
    }
}
