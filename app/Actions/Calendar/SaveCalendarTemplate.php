<?php

namespace App\Actions\Calendar;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodType;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SaveCalendarTemplate
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Save an organization's calendar shape and its automation policy.
     *
     * @param  array{name: string, description?: string|null, cycle_length_days: int, is_default?: bool, auto_open?: bool, generate_ahead_weeks?: int, remind_days_before?: int, periods: array<int, array<string, mixed>>}  $attributes
     */
    public function save(Organization $organization, array $attributes, ?CalendarTemplate $template = null, ?User $actor = null): CalendarTemplate
    {
        if ($template !== null && $template->organization_id !== $organization->id) {
            throw new InvalidValueException('That calendar template belongs to another organization.');
        }

        $periods = $this->periods($attributes['periods']);

        if ($periods === []) {
            throw new InvalidValueException('Add at least one period to the calendar template.');
        }

        $this->failIfPeriodsDoNotFit($periods, (int) $attributes['cycle_length_days']);

        return DB::transaction(function () use ($organization, $attributes, $template, $actor, $periods): CalendarTemplate {
            $values = Arr::only($attributes, [
                'name', 'description', 'cycle_length_days', 'is_default', 'auto_open', 'generate_ahead_weeks', 'remind_days_before',
            ]);
            $values['is_default'] = (bool) ($attributes['is_default'] ?? false);
            $values['auto_open'] = (bool) ($attributes['auto_open'] ?? false);
            $values['generate_ahead_weeks'] = (int) ($attributes['generate_ahead_weeks'] ?? 0);
            $values['remind_days_before'] = (int) ($attributes['remind_days_before'] ?? 14);

            if ($values['is_default']) {
                $organization->calendarTemplates()
                    ->when($template !== null, fn ($query) => $query->whereKeyNot($template->id))
                    ->update(['is_default' => false]);
            }

            $template ??= $organization->calendarTemplates()->create([
                ...$values,
                'created_by' => $actor?->id,
            ]);

            if ($template->exists) {
                $template->fill($values)->save();
            }

            $template->periods()->delete();
            $this->writePeriods($template, $periods);

            $this->auditor->record(
                AuditAction::CalendarTemplateSaved,
                $template,
                [
                    'organization_id' => $organization->id,
                    'period_count' => count($periods),
                    'is_default' => $template->is_default,
                ],
                $actor,
            );

            return $template->refresh();
        }, attempts: 3);
    }

    /**
     * Remove blank form rows. Each kept row is keyed by its row number as the form showed it, which is what a parent row names.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function periods(array $rows): array
    {
        $kept = [];

        foreach (array_values($rows) as $index => $row) {
            if (filled($row['name'] ?? null)) {
                $kept[$index + 1] = $row;
            }
        }

        return $kept;
    }

    /**
     * Check that every period fits the year, that a sub-period fits its parent, and that periods side by side never share a day.
     *
     * Generated periods skip the checks a period typed by hand passes, so a
     * template that breaks them would write a year where "the period that
     * covers today" has two answers.
     *
     * @param  array<int, array<string, mixed>>  $rows  keyed by row number
     *
     * @throws InvalidValueException
     */
    private function failIfPeriodsDoNotFit(array $rows, int $cycleLengthDays): void
    {
        $spans = [];

        foreach ($rows as $rowNumber => $row) {
            $start = (int) ($row['start_offset_days'] ?? 0);
            $end = $start + max((int) ($row['length_days'] ?? 1), 1) - 1;
            $parentIndex = filled($row['parent_index'] ?? null) ? (int) $row['parent_index'] : null;

            if ($parentIndex !== null && ($parentIndex >= $rowNumber || !isset($rows[$parentIndex]))) {
                throw new InvalidValueException("Row $rowNumber must name an earlier, filled row as its parent.");
            }

            if ($end >= $cycleLengthDays) {
                throw new InvalidValueException("Row $rowNumber ends after the school year ends. Shorten it or start it earlier.");
            }

            if ($parentIndex !== null && ($start < $spans[$parentIndex]['start'] || $end > $spans[$parentIndex]['end'])) {
                throw new InvalidValueException("Row $rowNumber must fall inside row $parentIndex, its parent.");
            }

            foreach ($spans as $otherNumber => $other) {
                if ($other['parent'] === $parentIndex && $start <= $other['end'] && $end >= $other['start']) {
                    throw new InvalidValueException("Rows $otherNumber and $rowNumber share days. Periods side by side must not overlap.");
                }
            }

            $spans[$rowNumber] = ['start' => $start, 'end' => $end, 'parent' => $parentIndex];
        }
    }

    /**
     * Write parent rows before their sub-periods.
     *
     * @param  array<int, array<string, mixed>>  $rows  keyed by row number
     */
    private function writePeriods(CalendarTemplate $template, array $rows): void
    {
        $written = [];

        foreach ($rows as $rowNumber => $row) {
            $parentIndex = filled($row['parent_index'] ?? null) ? (int) $row['parent_index'] : null;

            if ($parentIndex !== null && !isset($written[$parentIndex])) {
                throw new InvalidValueException('Each sub-period must name an earlier period in the template as its parent.');
            }

            $written[$rowNumber] = $template->periods()->create([
                'parent_id' => $parentIndex === null ? null : $written[$parentIndex]->id,
                'name' => trim((string) $row['name']),
                'label' => filled($row['label'] ?? null) ? trim((string) $row['label']) : null,
                'type' => $row['type'] ?? AcademicPeriodType::Term,
                'position' => (int) ($row['position'] ?? $rowNumber),
                'start_offset_days' => (int) ($row['start_offset_days'] ?? 0),
                'length_days' => (int) ($row['length_days'] ?? 1),
            ]);
        }
    }
}
