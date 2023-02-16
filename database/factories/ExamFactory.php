<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start = $this->faker->dateTimeBetween('next Monday', 'next Monday +7 day');
        $stop = $this->faker->dateTimeBetween($start, $start->format('Y-m-d H:i:s').' +10 days');

        return [
            'name'                => $this->faker->word,
            'description'         => $this->faker->sentence,
            'semester_id'         => '1',
            'start_date'          => $start,
            'stop_date'           => $stop,
            'active'              => $this->faker->boolean(),
            'publish_result'      => $this->faker->boolean(),
        ];
    }
}
