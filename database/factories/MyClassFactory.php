<?php

namespace Database\Factories;

use App\Models\ClassGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class MyClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $classGroup = ClassGroup::query()->where('school_id', 1)->inRandomOrder()->first();

        return [
            'name'           => $this->faker->name,
            'class_group_id' => $classGroup->id,
        ];
    }
}
