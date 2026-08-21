<?php

namespace Database\Factories;

use App\Models\CustomTimetableItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomTimetableItem>
 */
class CustomTimetableItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->name(),
            'school_id' => 1,
        ];
    }
}
