<?php

namespace Database\Factories;

use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryLoan>
 */
class LibraryLoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $copy = LibraryCopy::factory()->create();

        return [
            'school_id' => $copy->school_id,
            'library_copy_id' => $copy->id,
            'user_id' => User::factory(),
            'issued_on' => now()->toDateString(),
            'due_on' => now()->addDays(14)->toDateString(),
        ];
    }
}
