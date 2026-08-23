<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityBooking>
 */
class FacilityBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $facility = Facility::factory()->create();

        return [
            'school_id' => $facility->school_id,
            'facility_id' => $facility->id,
            'starts_at' => now()->addDay()->setTime(9, 0),
            'ends_at' => now()->addDay()->setTime(11, 0),
            'purpose' => 'Assembly',
        ];
    }
}
