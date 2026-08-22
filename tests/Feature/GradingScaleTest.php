<?php

namespace Tests\Feature;

use App\Models\GradingScale;
use App\Models\School;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingScaleTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_school_administrator_can_create_a_scored_grading_scale(): void
    {
        $this->authorized_user(['manage grading scale']);

        $this->post(route('grading-scales.store'), [
            'name'        => 'Primary grades',
            'description' => 'Used for continuous assessment.',
            'is_active'   => true,
            'options'     => [
                ['label' => 'Excellent', 'points' => 5],
                ['label' => 'Secure', 'points' => 3],
                ['label' => 'Developing', 'points' => 1],
            ],
        ])->assertRedirect(route('grading-scales.index'));

        $scale = GradingScale::query()->inSchool()->firstOrFail();
        $this->assertSame('Primary grades', $scale->name);
        $this->assertSame(['Excellent', 'Secure', 'Developing'], $scale->options()->pluck('label')->all());
        $this->assertSame([5.0, 3.0, 1.0], $scale->options()->pluck('points')->map(fn ($points): float => (float) $points)->all());
    }

    public function test_scale_options_must_be_all_scored_or_all_descriptive(): void
    {
        $this->authorized_user(['manage grading scale']);

        $this->from(route('grading-scales.index'))
            ->post(route('grading-scales.store'), [
                'name'    => 'Mixed scale',
                'options' => [
                    ['label' => 'Excellent', 'points' => 5],
                    ['label' => 'Secure', 'points' => null],
                ],
            ])
            ->assertRedirect(route('grading-scales.index'))
            ->assertSessionHasErrors('options');
    }

    public function test_a_scale_from_another_school_cannot_be_changed(): void
    {
        $this->authorized_user(['manage grading scale']);
        $otherSchool = School::query()->findOrFail(School::factory()->create()->getKey());
        $scale = GradingScale::factory()->create(['school_id' => $otherSchool->id]);

        $this->put(route('grading-scales.update', $scale), [
            'name'    => 'Changed',
            'options' => [
                ['label' => 'One', 'points' => null],
                ['label' => 'Two', 'points' => null],
            ],
        ])->assertForbidden();
    }
}
