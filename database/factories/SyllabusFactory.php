<?php

namespace Database\Factories;

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
    public function definition()
    {
        $file = UploadedFile::fake()->create("{$this->faker->name}.pdf")->store('pdfs');

        return [
            'name'        => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'subject_id'  => 1,
            'semester_id' => 1,
            'file'        => $file,
        ];
    }
}
