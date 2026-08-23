<?php

namespace Database\Factories;

use App\Models\LibraryTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryTitle>
 */
class LibraryTitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => null,
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'authors' => fake()->name(),
            'isbn' => fake()->unique()->isbn13(),
            'category' => fake()->randomElement(['Fiction', 'Science', 'History', 'Reference']),
            'published_year' => fake()->numberBetween(1970, 2025),
        ];
    }
}
