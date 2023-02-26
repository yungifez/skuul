<?php

namespace Database\Seeders;

use App\Models\FeeInvoiceRecord;
use Illuminate\Database\Seeder;

class FeeInvoiceRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeeInvoiceRecord::factory()->count(50)->create();
    }
}
