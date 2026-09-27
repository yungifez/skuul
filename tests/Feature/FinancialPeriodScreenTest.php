<?php

namespace Tests\Feature;

use App\Enums\FinancialPeriodStatus;
use App\Livewire\ManageFinancialPeriods;
use App\Models\FinancialPeriod;
use App\Models\School;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The bursar opens and closes the periods money is posted to.
 */
class FinancialPeriodScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_bursar_adds_a_period(): void
    {
        $this->authorized_user(['manage financial period']);

        Livewire::test(ManageFinancialPeriods::class)
            ->set('isAdding', true)
            ->set('name', '2027 financial year')
            ->set('startsOn', '2027-01-01')
            ->set('endsOn', '2027-12-31')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isAdding', false)
            ->assertSee('2027 financial year');

        $this->assertDatabaseHas('financial_periods', [
            'school_id' => $this->workingSchool()->id,
            'name' => '2027 financial year',
        ]);
    }

    public function test_a_period_cannot_end_before_it_starts_or_reuse_a_name(): void
    {
        $this->authorized_user(['manage financial period']);
        $this->period('Taken');

        Livewire::test(ManageFinancialPeriods::class)
            ->set('name', 'Taken')
            ->set('startsOn', '2027-12-31')
            ->set('endsOn', '2027-01-01')
            ->call('save')
            ->assertHasErrors(['name' => 'unique', 'endsOn' => 'after_or_equal']);
    }

    public function test_the_bursar_closes_and_reopens_a_period(): void
    {
        $this->authorized_user(['manage financial period']);
        $period = $this->period('Term one');

        $component = Livewire::test(ManageFinancialPeriods::class)
            ->call('close', $period->id)
            ->assertHasNoErrors()
            ->assertSee('Reopen');
        $this->assertSame(FinancialPeriodStatus::Closed, $period->fresh()->status);

        $component->call('reopen', $period->id)->assertHasNoErrors();
        $this->assertSame(FinancialPeriodStatus::Open, $period->fresh()->status);
    }

    /**
     * Before this screen, anyone on the finance page saw Close and Reopen.
     */
    public function test_someone_without_the_permission_sees_no_controls_and_cannot_close(): void
    {
        $this->authorized_user(['read fee invoice']);
        $period = $this->period('Term one');

        Livewire::test(ManageFinancialPeriods::class)
            ->assertSee('Term one')
            ->assertDontSee('Add period')
            ->assertDontSee('Close')
            ->call('close', $period->id)
            ->assertForbidden();

        $this->assertSame(FinancialPeriodStatus::Open, $period->fresh()->status);
    }

    public function test_a_period_of_another_school_cannot_be_closed(): void
    {
        $this->authorized_user(['manage financial period']);
        $other = FinancialPeriod::create([
            'school_id' => School::factory()->create()->id,
            'name' => 'Elsewhere',
            'starts_on' => now()->startOfYear()->toDateString(),
            'ends_on' => now()->endOfYear()->toDateString(),
        ]);

        $this->assertThrows(
            fn () => Livewire::test(ManageFinancialPeriods::class)->call('close', $other->id),
            ModelNotFoundException::class,
        );

        $this->assertSame(FinancialPeriodStatus::Open, $other->fresh()->status);
    }

    private function period(string $name): FinancialPeriod
    {
        return FinancialPeriod::create([
            'school_id' => $this->workingSchool()->id,
            'name' => $name,
            'starts_on' => now()->startOfYear()->toDateString(),
            'ends_on' => now()->endOfYear()->toDateString(),
        ]);
    }
}
