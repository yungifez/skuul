<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notice>
 */
class NoticeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeThisYear('+2 months');
        $stopDate = $startDate->add(date_interval_create_from_date_string('10 days'));

        return [
            'title'      => $this->faker->sentence,
            'content'    => $this->faker->paragraph,
            'attachment' => $this->faker->imageUrl(),
            'start_date' => $startDate->format('Y-m-d'),
            'stop_date'  => $stopDate->format('Y-m-d'),
            'active'     => $this->faker->boolean,
            'school_id'  => 1,
        ];
    }
}
