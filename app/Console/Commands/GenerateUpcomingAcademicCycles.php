<?php

namespace App\Console\Commands;

use App\Actions\Calendar\GenerateAcademicCycle;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\CalendarTemplate;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Build next year's calendar before this one runs out.
 *
 * A campus that reaches the last week of its cycle with nothing after it has
 * nowhere to put next term's timetable. The generated cycle is a draft, so
 * staff still read the dates before anybody teaches to them.
 */
class GenerateUpcomingAcademicCycles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:generate-upcoming-cycles {--dry-run : List what would be generated without writing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Draft the next academic cycle for campuses whose current one is ending';

    /**
     * Execute the console command.
     */
    public function handle(GenerateAcademicCycle $generator): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $generated = 0;

        foreach (School::all() as $school) {
            $template = $school->effectiveCalendarTemplate();

            if ($template === null || !$template->generatesAhead()) {
                continue;
            }

            $startsOn = $this->nextCycleStart($school, $template);

            if ($startsOn === null) {
                continue;
            }

            if ($dryRun) {
                $this->line("would draft a cycle at {$school->name} starting {$startsOn->toDateString()}");
                $generated++;

                continue;
            }

            try {
                $year = $generator->generate($school, $startsOn, $template);
                $this->info("drafted {$year->name} at {$school->name}");
                $generated++;
            } catch (InvalidValueException $exception) {
                $this->warn("skipped {$school->name}: {$exception->getMessage()}");
            }
        }

        $this->info($dryRun ? "{$generated} cycle(s) would be drafted." : "{$generated} cycle(s) drafted.");

        return self::SUCCESS;
    }

    /**
     * Work out when the next cycle should start, or null when it is too early.
     *
     * The next cycle starts the day after the current one ends. It is drafted
     * once the campus is inside the window the organization asked for, and
     * only when nothing already covers that day.
     */
    private function nextCycleStart(School $school, CalendarTemplate $template): ?Carbon
    {
        $latest = AcademicYear::where('school_id', $school->id)
            ->whereNotNull('ends_on')
            ->orderByDesc('ends_on')
            ->first();

        if ($latest === null || $latest->ends_on === null) {
            return null;
        }

        $window = $latest->ends_on->copy()->subWeeks($template->generate_ahead_weeks);

        if (now()->startOfDay()->lt($window)) {
            return null;
        }

        return $latest->ends_on->copy()->addDay()->startOfDay();
    }
}
