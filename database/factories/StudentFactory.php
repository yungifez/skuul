<?php

namespace Database\Factories;

use App\Models\MyClass;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class StudentFactory extends Factory
{
    protected $model = StudentRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $student = User::factory()->create();
        $class = MyClass::query()->inRandomOrder()->first();
        //if class doesnt have a section, create one (for testing sake)
        if ($class->sections->isEmpty() || $class->sections == null) {
            $class->sections()->create([
                'name' => $this->faker->name(),
            ]);
        }
        $student->assignRole('student');

        return [
            'user_id'          => $student->id,
            'my_class_id'      => $class->id,
            'section_id'       => $class->sections->first()->id ?? null,
            'admission_date'   => $this->faker->date(),
            'is_graduated'     => false,
            'admission_number' => Str::random(10),
        ];
    }
}
