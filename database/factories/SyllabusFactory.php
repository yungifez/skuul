<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Syllabus>
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
        $file = \Illuminate\Http\UploadedFile::fake()->create("{$this->faker->name}.pdf")->store('pdfs');

        return [
            'name'        => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'subject_id'  => 1,
            'semester_id' => 1,
            'file'        => $file,
        ];
    }
}
