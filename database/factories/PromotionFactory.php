<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'old_class_id'     => 1,
            'new_class_id'     => 2,
            'old_section_id'   => 1,
            'new_section_id'   => 2,
            'academic_year_id' => 1,
            'school_id'        => 1,
            'students'         => [4],
        ];
    }
}
