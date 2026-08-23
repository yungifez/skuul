<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Budget;
use App\Models\LedgerAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $academicYear = AcademicYear::factory()->create();

        return [
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'ledger_account_id' => LedgerAccount::factory()->create(['school_id' => $academicYear->school_id])->id,
            'amount' => fake()->randomFloat(2, 1000, 500000),
            'scope_hash' => str_repeat('0', 64),
        ];
    }
}
