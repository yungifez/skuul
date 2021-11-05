<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'   => $this->faker->name,
            'address'=> $this->faker->address,
            'initials'=> $this->faker->unique()->word,
            'code' => $this->faker->unique()->randomNumber(5),
        ];
    }
}
