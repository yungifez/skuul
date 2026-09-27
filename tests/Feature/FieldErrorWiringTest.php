<?php

namespace Tests\Feature;

use App\Livewire\CreateCohortForm;
use App\Livewire\CreateFeeCategoryForm;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        // The controls are written by hand, so the view carries the wiring.
        $this->assertMatchesRegularExpression(
            '/<textarea[^>]*\bid="description"[^>]*aria-invalid="true"/s',
            $html,
            'The description field must announce that it was refused.'
        );

        foreach (['name', 'type', 'description'] as $field) {
            $this->assertMatchesRegularExpression('/\baria-describedby="'.$field.'-error"/', $html, "The {$field} field must point at its message.");
            $this->assertStringContainsString('id="'.$field.'-error"', $html);
        }

        $this->assertSame(3, substr_count($html, 'aria-invalid="true"'));
    }

    public function test_a_refused_april_component_points_at_its_own_message(): void
    {
        $this->authorized_user(['create fee category']);

        // A blade directive inside an <april:*> tag breaks the tag
        // precompiler, so these components read their own binding instead.
        $html = Livewire::test(CreateFeeCategoryForm::class)
            ->set('name', 'Tuition')
            ->set('description', str_repeat('a', 10001))
            ->call('save')
            ->assertHasErrors(['description'])
            ->html();

        $this->assertMatchesRegularExpression('/<textarea[^>]*\baria-describedby="description-error"/s', $html);
        $this->assertSame(1, substr_count($html, 'aria-invalid="true"'));
    }

    public function test_a_field_that_passed_says_nothing(): void
    {
        $this->authorized_user(['read cohort', 'create cohort']);

        $html = Livewire::test(CreateCohortForm::class)->html();

        $this->assertStringNotContainsString('aria-invalid', $html);
        $this->assertStringNotContainsString('-error"', $html);
    }

    /**
     * Render the group form after it refused every field on it.
     */
    private function refusedCohortForm(): string
    {
        $this->authorized_user(['read cohort', 'create cohort']);

        return Livewire::test(CreateCohortForm::class)
            ->set('name', '')
            ->set('type', 'not-a-type')
            ->set('description', str_repeat('a', 1001))
            ->call('save')
            ->assertHasErrors(['name', 'type', 'description'])
            ->html();
    }
}
