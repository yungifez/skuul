<?php

namespace Tests\Feature;

use App\Console\Commands\RefreshDemoData;
use App\Livewire\ListNoticesTable;
use App\Models\Notice;
use App\Services\Demo\DemoDataRefresher;
use App\Traits\FeatureTestTrait;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_reset_refuses_to_run_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);
        $this->mock(DemoDataRefresher::class)->shouldNotReceive('refresh');

        $this->artisan(RefreshDemoData::class)
            ->expectsOutputToContain('Demo mode is off')
            ->assertFailed();
    }

    public function test_the_reset_runs_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $this->mock(DemoDataRefresher::class)->shouldReceive('refresh')->once();

        $this->artisan(RefreshDemoData::class)
            ->expectsOutputToContain('The demo school is reset.')
            ->assertSuccessful();
    }

    public function test_the_reset_keeps_the_migration_history_and_the_world_data(): void
    {
        $preserved = app(DemoDataRefresher::class)->preservedTables();

        $this->assertSame(['migrations', 'countries', 'states', 'cities', 'timezones', 'currencies', 'languages'], $preserved);
    }

    public function test_the_reset_is_scheduled_hourly_only_in_demo_mode(): void
    {
        $reset = collect(app(Schedule::class)->events())
            ->first(fn (Event $event): bool => str_contains((string) $event->command, 'skuul:refresh-demo'));

        $this->assertNotNull($reset);
        $this->assertSame('0 * * * *', $reset->expression);

        config(['demo.enabled' => false]);
        $this->assertFalse($reset->filtersPass($this->app));

        config(['demo.enabled' => true]);
        $this->assertTrue($reset->filtersPass($this->app));
    }

    public function test_a_visitor_cannot_delete_a_record_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice', 'delete notice']);

        Livewire::test(ListNoticesTable::class)
            ->call('deleteNotice', $notice->id)
            ->assertDispatched('status-message', type: 'danger', message: 'Deleting is turned off in the demo. Every other change works, and the demo resets every hour.');

        $this->assertModelExists($notice);
    }

    public function test_a_visitor_can_delete_a_record_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice', 'delete notice']);

        Livewire::test(ListNoticesTable::class)->call('deleteNotice', $notice->id);

        $this->assertModelMissing($notice);
    }

    public function test_a_command_can_still_delete_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);

        $notice->delete();

        $this->assertModelMissing($notice);
    }

    public function test_the_sign_in_page_offers_the_demo_accounts_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Try the demo as')
            ->assertSee('School administrator')
            ->assertSee('teacher@example.com')
            ->assertDontSee('creates your account and emails you an invitation link');
    }

    public function test_the_sign_in_page_hides_the_demo_accounts_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Try the demo as')
            ->assertDontSee('teacher@example.com')
            ->assertSee('creates your account and emails you an invitation link');
    }
}
