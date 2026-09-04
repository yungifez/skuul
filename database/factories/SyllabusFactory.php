<?php

namespace Database\Factories;

use App\Models\CourseOffering;
use App\Models\Syllabus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<Syllabus>
 */
class SyllabusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = UploadedFile::fake()->create(fake()->name().'.pdf')->store('pdfs');

        return [
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'course_offering_id' => CourseOffering::factory(),
            'file' => $file,
        ];
    }
}
