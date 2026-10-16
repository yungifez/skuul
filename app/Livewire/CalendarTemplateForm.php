<?php

namespace App\Livewire;

use App\Actions\Calendar\SaveCalendarTemplate;
use App\Enums\AcademicPeriodType;
use App\Exceptions\InvalidValueException;
use App\Models\CalendarTemplate;
use App\Models\CalendarTemplatePeriod;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Shape an organization's school year once, so every campus can generate its years from it.
 */
class CalendarTemplateForm extends Component
{
    private const MAX_PERIODS = 12;

    #[Locked]
    public Organization $organization;

    #[Locked]
    public ?CalendarTemplate $calendarTemplate = null;

    public string $name = '';

    public string $description = '';

    public string $cycleLengthDays = '365';

    public bool $isDefault = false;

    public bool $autoOpen = false;

    public string $generateAheadWeeks = '0';

    public string $remindDaysBefore = '14';

    /**
     * One row per period. A parent is named by its row number, counting from 1.
     *
     * @var array<int, array{name: string, label: string, type: string, position: string, start_offset_days: string, length_days: string, parent_index: string}>
     */
    public array $periods = [];

    public function mount(Organization $organization, ?CalendarTemplate $calendarTemplate = null): void
    {
        Gate::authorize('manageCalendar', $organization);

        $this->organization = $organization;

        if ($calendarTemplate?->exists !== true) {
            $this->periods = [
                $this->row('Term 1', 'term', 1, 0, 84),
                $this->row('Term 2', 'term', 2, 112, 84),
                $this->row('Term 3', 'term', 3, 224, 84),
            ];

            return;
        }

        abort_unless($calendarTemplate->organization_id === $organization->id, 404);

        $this->calendarTemplate = $calendarTemplate;
        $this->name = $calendarTemplate->name;
        $this->description = (string) $calendarTemplate->description;
        $this->cycleLengthDays = (string) $calendarTemplate->cycle_length_days;
        $this->isDefault = $calendarTemplate->is_default;
        $this->autoOpen = $calendarTemplate->auto_open;
        $this->generateAheadWeeks = (string) $calendarTemplate->generate_ahead_weeks;
        $this->remindDaysBefore = (string) $calendarTemplate->remind_days_before;
        $this->periods = $this->storedRows($calendarTemplate->periods()->get());
    }

    public function addPeriod(): void
    {
        if (count($this->periods) >= self::MAX_PERIODS) {
            return;
        }

        $last = end($this->periods);
        $start = $last === false ? 0 : (int) $last['start_offset_days'] + max((int) $last['length_days'], 1);

        $this->periods[] = $this->row('', 'term', count($this->periods) + 1, $start, 84);
    }

    /**
     * Remove a row. Rows under it lose their parent, and later parent numbers move up by one.
     */
    public function removePeriod(int $index): void
    {
        if (!array_key_exists($index, $this->periods) || count($this->periods) === 1) {
            return;
        }

        $removedNumber = $index + 1;
        unset($this->periods[$index]);

        $this->periods = array_values(array_map(function (array $row) use ($removedNumber): array {
            $parent = (int) $row['parent_index'];

            if ($parent === $removedNumber) {
                $row['parent_index'] = '';
            } elseif ($parent > $removedNumber) {
                $row['parent_index'] = (string) ($parent - 1);
            }

            return $row;
        }, $this->periods));

        $this->resetValidation();
    }

    public function save(SaveCalendarTemplate $saveCalendarTemplate): void
    {
        Gate::authorize('manageCalendar', $this->organization);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'cycleLengthDays' => ['required', 'integer', 'min:1', 'max:3660'],
            'isDefault' => ['boolean'],
            'autoOpen' => ['boolean'],
            'generateAheadWeeks' => ['required', 'integer', 'min:0', 'max:104'],
            'remindDaysBefore' => ['required', 'integer', 'min:0', 'max:90'],
            'periods' => ['required', 'array', 'min:1', 'max:'.self::MAX_PERIODS],
            'periods.*.name' => ['required', 'string', 'max:100'],
            'periods.*.label' => ['nullable', 'string', 'max:100'],
            'periods.*.type' => ['required', Rule::in(AcademicPeriodType::values())],
            'periods.*.position' => ['required', 'integer', 'min:1', 'max:99'],
            'periods.*.start_offset_days' => ['required', 'integer', 'min:0', 'max:3660'],
            'periods.*.length_days' => ['required', 'integer', 'min:1', 'max:3660'],
            'periods.*.parent_index' => ['nullable', 'integer', 'min:1', 'max:'.self::MAX_PERIODS],
        ], [
            'periods.*.name.required' => 'Name this period, or remove the row.',
        ], [
            'cycleLengthDays' => 'school year length',
            'generateAheadWeeks' => 'weeks ahead',
            'remindDaysBefore' => 'reminder lead time',
            'periods.*.name' => 'period name',
            'periods.*.label' => 'local label',
            'periods.*.position' => 'display order',
            'periods.*.start_offset_days' => 'start day',
            'periods.*.length_days' => 'length',
            'periods.*.parent_index' => 'parent row',
        ]);

        try {
            $saved = $saveCalendarTemplate->save($this->organization, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'cycle_length_days' => (int) $this->cycleLengthDays,
                'is_default' => $this->isDefault,
                'auto_open' => $this->autoOpen,
                'generate_ahead_weeks' => (int) $this->generateAheadWeeks,
                'remind_days_before' => (int) $this->remindDaysBefore,
                'periods' => $this->periods,
            ], $this->calendarTemplate, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('periods', $exception->getMessage());

            return;
        }

        session()->flash('success', $this->calendarTemplate === null
            ? 'Calendar template created. Review its periods before generating a cycle.'
            : 'Calendar template saved.');

        $this->redirectRoute('organizations.calendar-templates.edit', [$this->organization, $saved]);
    }

    public function render(): View
    {
        return view('livewire.calendar-template-form', [
            'periodTypes' => AcademicPeriodType::cases(),
            'canAddPeriod' => count($this->periods) < self::MAX_PERIODS,
        ]);
    }

    /**
     * Put the stored periods in rows, each parent before the periods inside it, so a parent row is always an earlier row.
     *
     * @param  Collection<int, CalendarTemplatePeriod>  $periods  in display order
     * @return array<int, array{name: string, label: string, type: string, position: string, start_offset_days: string, length_days: string, parent_index: string}>
     */
    private function storedRows(Collection $periods): array
    {
        $rows = [];
        $rowNumbers = [];
        $children = $periods->groupBy(fn (CalendarTemplatePeriod $period): string => (string) $period->parent_id);
        $knownIds = $periods->pluck('id')->all();

        $place = function (CalendarTemplatePeriod $period) use (&$place, &$rows, &$rowNumbers, $children): void {
            $row = $this->row($period->name, $period->type->value, $period->position, $period->start_offset_days, $period->length_days);
            $row['label'] = (string) $period->label;
            $row['parent_index'] = $period->parent_id === null ? '' : (string) ($rowNumbers[$period->parent_id] ?? '');
            $rows[] = $row;
            $rowNumbers[$period->id] = count($rows);

            foreach ($children->get((string) $period->id, []) as $child) {
                $place($child);
            }
        };

        $periods
            ->filter(fn (CalendarTemplatePeriod $period): bool => $period->parent_id === null || !in_array($period->parent_id, $knownIds, true))
            ->each($place);

        return $rows;
    }

    /**
     * @return array{name: string, label: string, type: string, position: string, start_offset_days: string, length_days: string, parent_index: string}
     */
    private function row(string $name, string $type, int $position, int $startOffsetDays, int $lengthDays): array
    {
        return [
            'name' => $name,
            'label' => '',
            'type' => $type,
            'position' => (string) $position,
            'start_offset_days' => (string) $startOffsetDays,
            'length_days' => (string) $lengthDays,
            'parent_index' => '',
        ];
    }
}
