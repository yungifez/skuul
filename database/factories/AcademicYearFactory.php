<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $year = $this->faker->year;

        return [
            'start_year' => $year,
            'stop_year'  => $year + 1,
            'school_id'  => 1,
        ];
    }
}
