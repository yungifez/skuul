<?php

namespace App\Console\Commands;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Enums\AcademicPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Open due periods for organizations with automatic advancement enabled.
 * Never close periods automatically; closure freezes records and requires confirmation.
 */
class AdvanceAcademicCalendar extends Command
{
    /**
     * @var string
     */
    protected $signature = 'skuul:advance-academic-calendar {--dry-run : List what would open without opening it}';

    /**
     * @var string
     */
    protected $description = 'Open scheduled academic periods whose start date has arrived';

    public function handle(ChangeAcademicPeriodStatus $lifecycle): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $opened = 0;

        foreach ($this->campusesThatAutoOpen() as $school) {
            foreach ($this->duePeriods($school) as $period) {
                $label = $period instanceof AcademicYear
                    ? "cycle {$period->name}"
                    : "period {$period->displayName}";

                if ($dryRun) {
                    $this->line("would open {$label} at {$school->name}");
                    $opened++;

                    continue;
                }

                try {
                    $lifecycle->open($period);
                    $this->info("opened {$label} at {$school->name}");
                    $opened++;
                } catch (InvalidValueException $exception) {
                    // A period whose cycle is still closed is not an error
                    // here. Report it and carry on with the rest.
                    $this->warn("skipped {$label} at {$school->name}: {$exception->getMessage()}");
                }
            }
        }

        $this->info($dryRun ? "{$opened} period(s) would open." : "{$opened} period(s) opened.");

        return self::SUCCESS;
    }

    /**
     * Get the campuses whose calendar policy allows an automatic open.
     *
     * @return Collection<int, School>
     */
    private function campusesThatAutoOpen(): Collection
    {
        return School::with('organization')->get()->filter(
            fn (School $school): bool => (bool) $school->effectiveCalendarTemplate()?->auto_open
        );
    }

    /**
     * Get the cycles and periods of a campus that should be open today.
     *
     * A cycle opens before the periods inside it, so the cycles come first.
     *
     * @return Collection<int, AcademicYear|AcademicPeriod>
     */
    private function duePeriods(School $school): Collection
    {
        $today = now()->toDateString();

        $cycles = AcademicYear::where('school_id', $school->id)
            ->where('status', AcademicPeriodStatus::Scheduled)
            ->whereNotNull('starts_on')
            ->whereDate('starts_on', '<=', $today)
            ->get();

        $periods = AcademicPeriod::where('school_id', $school->id)
            ->where('status', AcademicPeriodStatus::Scheduled)
            ->whereNotNull('starts_on')
            ->whereDate('starts_on', '<=', $today)
            ->ordered()
            ->get();

        return $cycles->concat($periods);
    }
}
