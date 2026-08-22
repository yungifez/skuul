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
        $file = UploadedFile::fake()->create("{$this->faker->name}.pdf")->store('pdfs');

        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'course_offering_id' => CourseOffering::factory(),
            'file' => $file,
        ];
    }
}
