<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\FeeInvoice;
use App\Models\StudentRecord;
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
            // Seeding runs outside a request, so read the enrollment straight
            // from its school instead of the request-scoped relation. Only
            // look for a student when the caller does not name one.
            'user_id' => fn () => StudentRecord::query()
                ->where('school_id', 1)
                ->where('status', EnrollmentStatus::Active)
                ->inRandomOrder()
                ->value('user_id'),
            'issue_date' => $issueDate,
            'due_date'   => $dueDate,
        ];
    }
}
