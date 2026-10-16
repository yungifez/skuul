<?php

namespace Database\Factories;

use App\Models\BillingGroup;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillingGroup>
 */
class BillingGroupFactory extends Factory
{
    /** @var class-string<BillingGroup> */
    protected $model = BillingGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->unique()->words(2, true).' purse',
        ];
    }
}
