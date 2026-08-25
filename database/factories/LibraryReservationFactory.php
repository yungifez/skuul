<?php

namespace Database\Factories;

use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryReservation>
 */
class LibraryReservationFactory extends Factory
{
    /** @var class-string<LibraryReservation> */
    protected $model = LibraryReservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'library_title_id' => LibraryTitle::factory(),
            'user_id' => User::factory(),
            'reserved_on' => now()->toDateString(),
        ];
    }
}
