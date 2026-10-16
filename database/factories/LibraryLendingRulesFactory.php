<?php

namespace Database\Factories;

use App\Models\LibraryLendingRules;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryLendingRules>
 */
class LibraryLendingRulesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::first()?->id ?? School::factory(),
        ];
    }
}
