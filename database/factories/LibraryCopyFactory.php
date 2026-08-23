<?php

namespace Database\Factories;

use App\Models\LibraryCopy;
use App\Models\LibraryTitle;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryCopy>
 */
class LibraryCopyFactory extends Factory
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
            'library_title_id' => LibraryTitle::factory(),
            'barcode' => fake()->unique()->bothify('LIB-########'),
        ];
    }
}
