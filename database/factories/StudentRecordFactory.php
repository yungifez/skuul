<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\Model;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Model>
 */
class StudentRecordFactory extends Factory
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
        $section = Section::query()->offset(rand(1, 4))->whereRelation('myClass.classGroup', 'school_id', 1)->first();
        $class = $section->myClass;
        $student->assignRole('student');

        return [
            'user_id' => $student->id,
            'school_id' => $class->classGroup->school_id,
            'my_class_id' => $class->id,
            'section_id' => $class->sections->first()->id ?? null,
            'admission_date' => $this->faker->date(),
            'status' => EnrollmentStatus::Active,
            'admission_number' => Str::random(10),
        ];
    }
}
