<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GradeSystem>
 */
class GradeSystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $grade_from = $this->faker->numberBetween(100, 1000);

        return [
            'name'           => $this->faker->word,
            'remark'         => $this->faker->sentence,
            'class_group_id' => '1',
            'grade_from'     => $grade_from,
            'grade_till'     => $grade_from = 100,
        ];
    }
}
