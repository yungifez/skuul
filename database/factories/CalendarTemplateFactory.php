<?php

namespace Database\Factories;

use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarTemplate>
 */
class CalendarTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Three-term calendar',
            'description' => $this->faker->sentence(),
            'is_default' => false,
            'cycle_length_days' => 365,
            'auto_open' => false,
            'generate_ahead_weeks' => 0,
            'remind_days_before' => 14,
            'created_by' => User::factory(),
        ];
    }
}
