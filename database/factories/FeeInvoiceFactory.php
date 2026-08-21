<?php

namespace Database\Factories;

use App\Models\FeeInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeeInvoice>
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
        $issueDate = $this->faker->dateTimeThisYear('+2 months');
        $days = mt_rand(10, 50);
        $dueDate = Carbon::instance($issueDate)->addDays($days);

        return [
            'name' => $this->faker->name(),
            'note' => $this->faker->sentence(),
            // Roles are held per school, so only look for a student when the
            // caller does not name one.
            'user_id'    => fn () => User::ofSchool(1)->students()->activeStudents()->inRandomOrder()->first()?->id,
            'issue_date' => $issueDate,
            'due_date'   => $dueDate,
        ];
    }
}
