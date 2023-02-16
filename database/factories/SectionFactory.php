<?php

namespace Database\Factories;

use App\Models\MyClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $class = MyClass::query()->whereRelation('classGroup', 'school_id', 1)->inRandomOrder()->first();

        return [
            'name'        => $this->faker->name,
            'my_class_id' => $class->id,
        ];
    }
}
