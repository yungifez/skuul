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
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Save an organization's calendar shape and its automation policy.
     *
     * @param array{name: string, description?: string|null, cycle_length_days: int, is_default?: bool, auto_open?: bool, generate_ahead_weeks?: int, remind_days_before?: int, periods: array<int, array<string, mixed>>} $attributes
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
                    'period_count'    => count($periods),
                    'is_default'      => $template->is_default,
                ],
                $actor,
            );

            return $template->refresh();
        }, attempts: 3);
    }

    /**
     * Remove blank form rows and keep their submitted index for parent links.
     *
     * @param array<int, array<string, mixed>> $rows
     *
     * @return array<int, array<string, mixed>>
     */
    private function periods(array $rows): array
    {
        return array_filter($rows, fn (array $row): bool => filled($row['name'] ?? null));
    }

    /**
     * Write parent rows before their sub-periods.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    private function writePeriods(CalendarTemplate $template, array $rows): void
    {
        $written = [];
        $rowNumber = 0;

        foreach ($rows as $row) {
            $rowNumber++;
            $parentIndex = filled($row['parent_index'] ?? null) ? (int) $row['parent_index'] : null;

            if ($parentIndex !== null && !isset($written[$parentIndex])) {
                throw new InvalidValueException('Each sub-period must name an earlier period in the template as its parent.');
            }

            $written[$rowNumber] = $template->periods()->create([
                'parent_id'         => $parentIndex === null ? null : $written[$parentIndex]->id,
                'name'              => $row['name'],
                'label'             => $row['label'] ?: null,
                'type'              => $row['type'] ?? AcademicPeriodType::Term,
                'position'          => (int) ($row['position'] ?? $rowNumber),
                'start_offset_days' => (int) ($row['start_offset_days'] ?? 0),
                'length_days'       => (int) ($row['length_days'] ?? 1),
            ]);
        }
    }
}
