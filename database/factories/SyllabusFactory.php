<?php

namespace Database\Factories;

use App\Models\CourseOffering;
use App\Models\Syllabus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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
        $faker = function_exists('fake') ? fake() : null;
        $fileName = $faker?->name() ?? 'demo-syllabus-'.Str::lower(Str::random(12));

        return [
            'name' => $faker?->sentence() ?? 'Demo syllabus',
            'description' => $faker?->paragraph() ?? 'Course plan and learning outcomes.',
            'course_offering_id' => CourseOffering::factory(),
            'file' => UploadedFile::fake()->create($fileName.'.pdf')->store('pdfs'),
        ];
    }
}
