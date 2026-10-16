<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\SchoolDomain;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SchoolDomain>
 */
class SchoolDomainFactory extends Factory
{
    /** @var class-string<SchoolDomain> */
    protected $model = SchoolDomain::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'school_id' => null,
            'host' => Str::lower(Str::random(8)).'.example.school',
            'is_primary' => false,
            'verification_token' => Str::lower(Str::random(32)),
            'verified_at' => null,
        ];
    }

    /**
     * An address the organization has proved it owns.
     */
    public function verified(): self
    {
        return $this->state(fn (): array => ['verified_at' => now()]);
    }
}
