<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A refused field has to say so on the control itself. A message sitting
 * loose on the page reaches nobody who cannot see where it is.
 */
class FieldErrorWiringTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_refused_native_control_points_at_its_own_message(): void
    {
        $html = $this->refusedCohortForm();

        // The textarea is written by hand, so the view carries the wiring.
        $this->assertMatchesRegularExpression(
            '/<textarea[^>]*\bname="description"[^>]*aria-invalid="true"/s',
            $html,
            'The description field must announce that it was refused.'
        );
        $this->assertMatchesRegularExpression(
            '/<textarea[^>]*\baria-describedby="description-error"/s',
            $html
        );
        $this->assertStringContainsString('id="description-error"', $html);
    }

    public function test_a_refused_april_component_points_at_its_own_message(): void
    {
        $html = $this->refusedCohortForm();

        // A blade directive inside an <april:*> tag breaks the tag
        // precompiler, so these components read their own binding instead.
        foreach (['name', 'type'] as $field) {
            $this->assertMatchesRegularExpression(
                '/\baria-describedby="'.$field.'-error"/',
                $html,
                "The {$field} field must point at its message."
            );
            $this->assertStringContainsString('id="'.$field.'-error"', $html);
        }

        $this->assertSame(3, substr_count($html, 'aria-invalid="true"'));
    }

    public function test_a_field_that_passed_says_nothing(): void
    {
        $actor = $this->authorized_user(['read cohort', 'create cohort']);
        $html = (string) $actor->get(route('cohorts.create'))->assertOk()->getContent();

        $this->assertStringNotContainsString('aria-invalid', $html);
        $this->assertStringNotContainsString('-error"', $html);
    }

    /**
     * Render the group form after it refused every field on it.
     */
    private function refusedCohortForm(): string
    {
        $actor = $this->authorized_user(['read cohort', 'create cohort']);

        $actor->from(route('cohorts.create'))
            ->post(route('cohorts.store'), [
                'name' => '',
                'type' => 'not-a-type',
                'description' => str_repeat('a', 1001),
            ])
            ->assertSessionHasErrors(['name', 'type', 'description']);

        return (string) $actor->get(route('cohorts.create'))->assertOk()->getContent();
    }
}
