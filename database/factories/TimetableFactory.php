<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timetable>
 */
class TimetableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id'          => $this->faker->numberBetween(1, 10000),
            'name'        => $this->faker->name,
            'description' => $this->faker->text,
            'my_class_id' => 1,
            'semester_id' => 1,
        ];
    }
}
