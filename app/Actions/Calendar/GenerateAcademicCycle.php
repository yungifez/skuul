<?php

namespace App\Actions\Calendar;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CalendarTemplate;
use App\Models\CalendarTemplatePeriod;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Build a dated academic cycle for a campus from a calendar template.
 *
 * The template says the shape; the start date turns it into real days. Every
 * period arrives as a draft, because a generated calendar is a proposal until
 * staff have read the dates.
 */
class GenerateAcademicCycle
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Generate the cycle and return the academic year that holds it.
     *
     * @param  array<string, mixed>  $overrides  values to force onto the year
     *
     * @throws InvalidValueException when the campus has no template to follow
     */
    public function generate(
        School $school,
        Carbon $startsOn,
        ?CalendarTemplate $template = null,
        ?User $actor = null,
        array $overrides = [],
    ): AcademicYear {
        $template ??= $school->effectiveCalendarTemplate();

        if ($template === null) {
            throw new InvalidValueException(
                'This campus has no calendar template. Choose one, or set a default for the organization.'
            );
        }

        if ($template->organization_id !== $school->organization_id) {
            throw new InvalidValueException('That calendar template belongs to another organization.');
        }

        $startsOn = $startsOn->copy()->startOfDay();
        $endsOn = $startsOn->copy()->addDays(max($template->cycle_length_days, 1) - 1);

        $this->refuseOverlap($school, $startsOn, $endsOn);

        return DB::transaction(function () use ($school, $template, $startsOn, $endsOn, $actor, $overrides): AcademicYear {
            $year = AcademicYear::create(array_merge([
                'school_id' => $school->id,
                'start_year' => (int) $startsOn->format('Y'),
                'stop_year' => (int) $endsOn->format('Y'),
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'status' => AcademicPeriodStatus::Draft,
            ], $overrides));

            $created = $this->createPeriods($template, $year, $school, $startsOn, null, null);

            $this->auditor->record(
                AuditAction::AcademicCycleGenerated,
                $year,
                [
                    'template_id' => $template->id,
                    'template_name' => $template->name,
                    'starts_on' => $startsOn->toDateString(),
                    'ends_on' => $endsOn->toDateString(),
                    'periods' => $created,
                ],
                $actor,
            );

            return $year->refresh();
        });
    }

    /**
     * Create the periods of one template level, then the levels inside them.
     *
     * @return int the number of periods written, this level and below
     */
    private function createPeriods(
        CalendarTemplate $template,
        AcademicYear $year,
        School $school,
        Carbon $cycleStart,
        ?CalendarTemplatePeriod $templateParent,
        ?AcademicPeriod $parent,
    ): int {
        $rows = $templateParent === null
            ? $template->topLevelPeriods()->get()
            : $templateParent->children()->get();

        $written = 0;

        foreach ($rows as $templatePeriod) {
            $period = AcademicPeriod::create([
                'school_id' => $school->id,
                'academic_year_id' => $year->id,
                'parent_id' => $parent?->id,
                'name' => $templatePeriod->name,
                'label' => $templatePeriod->label,
                'type' => $templatePeriod->type,
                'position' => $templatePeriod->position,
                'starts_on' => $templatePeriod->startsOn($cycleStart),
                'ends_on' => $templatePeriod->endsOn($cycleStart),
                'status' => AcademicPeriodStatus::Draft,
            ]);

            $written += 1 + $this->createPeriods($template, $year, $school, $cycleStart, $templatePeriod, $period);
        }

        return $written;
    }

    /**
     * Refuse a cycle that runs over one the campus already has.
     *
     * Two cycles covering one day would make "the period that covers today"
     * ambiguous, and every record that resolves its period by date would pick
     * whichever row came back first.
     *
     * @throws InvalidValueException when the dates overlap an existing cycle
     */
    private function refuseOverlap(School $school, Carbon $startsOn, Carbon $endsOn): void
    {
        $clash = AcademicYear::where('school_id', $school->id)
            ->whereNotNull('starts_on')
            ->whereNotNull('ends_on')
            ->whereDate('starts_on', '<=', $endsOn->toDateString())
            ->whereDate('ends_on', '>=', $startsOn->toDateString())
            ->first();

        if ($clash !== null) {
            throw new InvalidValueException(
                "This cycle runs over {$clash->name}, which covers {$clash->starts_on?->toDateString()} to {$clash->ends_on?->toDateString()}."
            );
        }
    }
}
