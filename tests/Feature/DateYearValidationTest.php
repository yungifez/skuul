<?php

namespace Tests\Feature;

use App\Livewire\CalendarEventEditor;
use App\Models\CalendarEvent;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * A slip on the year key must come back as a message, not a crash.
 */
class DateYearValidationTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * @return array<string, array{mixed, bool}>
     */
    public static function dates(): array
    {
        return [
            'an ordinary day' => ['2026-09-02', true],
            'a date and time' => ['2026-09-02T08:30', true],
            'a day-first date' => ['02-09-2026', true],
            'a written date' => ['2 September 2026', true],
            'a five-digit year' => ['20266-09-02', false],
            'a five-digit year with a time' => ['20266-09-02T08:30', false],
            'a year before the database range' => ['0026-09-02', false],
            'not a date' => ['next someday', false],
        ];
    }

    #[DataProvider('dates')]
    public function test_the_date_rule_keeps_the_year_inside_the_database(mixed $value, bool $passes): void
    {
        $this->assertSame($passes, Validator::make(['day' => $value], ['day' => 'date'])->passes());
    }

    public function test_a_carbon_date_still_passes(): void
    {
        $this->assertTrue(Validator::make(['day' => Carbon::parse('2026-09-02')], ['day' => 'date'])->passes());
    }

    public function test_a_form_answers_a_five_digit_year_with_a_message(): void
    {
        $this->authorized_user(['create calendar event', 'read calendar event']);

        Livewire::test(CalendarEventEditor::class)
            ->set('title', 'Sports day')
            ->set('startsAt', '20266-09-02')
            ->set('endsAt', '20266-09-02')
            ->call('save')
            ->assertHasErrors(['startsAt', 'endsAt']);

        $this->assertSame(0, CalendarEvent::query()->count());
    }
}
