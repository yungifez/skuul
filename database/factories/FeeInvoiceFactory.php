<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeInvoice>
 */
class FeeInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::where('school_id', 1)->students()->activeStudents()->inRandomOrder()->first();
        $issueDate = $this->faker->dateTimeThisYear('+2 months');
        $days = mt_rand(10, 50);
        $dueDate = Carbon::instance($issueDate)->addDays($days);

        return [
            'name'       => $this->faker->name(),
            'note'       => $this->faker->sentence(),
            'user_id'    => $student->id,
            'issue_date' => $issueDate,
            'due_date'   => $dueDate,
        ];
    }
}
