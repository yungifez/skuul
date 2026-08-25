<?php

namespace App\Livewire;

use App\Actions\Academic\SaveAcademicCalendar;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class AcademicCalendarForm extends Component
{
    public ?AcademicYear $academicYear = null;

    public string $startsOn = '';

    public string $endsOn = '';

    public string $structure = 'three_terms';

    /** @var array<int, array{name: string, type: string, starts_on: string, ends_on: string}> */
    public array $periods = [];

    public function mount(?AcademicYear $academicYear = null): void
    {
        $this->academicYear = $academicYear;

        if ($academicYear !== null) {
            $this->startsOn = $academicYear->starts_on?->toDateString() ?? '';
            $this->endsOn = $academicYear->ends_on?->toDateString() ?? '';
            $this->periods = $academicYear->topLevelPeriods()->get()->map(fn ($period): array => [
                'name' => $period->displayName,
                'type' => $period->type->value,
                'starts_on' => $period->starts_on?->toDateString() ?? '',
                'ends_on' => $period->ends_on?->toDateString() ?? '',
            ])->all();
            $this->structure = 'custom';

            return;
        }

        $this->startsOn = now()->startOfYear()->toDateString();
        $this->endsOn = now()->endOfYear()->toDateString();
        $this->generatePeriods();
    }

    public function updatedStructure(): void
    {
        $this->generatePeriods();
    }

    public function generatePeriods(): void
    {
        $this->validateOnly('startsOn', $this->dateRules());
        $this->validateOnly('endsOn', $this->dateRules());

        $count = match ($this->structure) {
            'two_semesters' => 2,
            'three_trimesters', 'three_terms' => 3,
            'four_quarters' => 4,
            default => 1,
        };
        $type = match ($this->structure) {
            'two_semesters' => AcademicPeriodType::Semester,
            'three_trimesters' => AcademicPeriodType::Trimester,
            'four_quarters' => AcademicPeriodType::Quarter,
            'custom' => AcademicPeriodType::Other,
            default => AcademicPeriodType::Term,
        };

        $firstDay = Carbon::parse($this->startsOn)->startOfDay();
        $lastDay = Carbon::parse($this->endsOn)->startOfDay();
        $intervalDays = $firstDay->diff($lastDay)->days;
        $totalDays = ($intervalDays === false ? 0 : $intervalDays) + 1;
        $baseLength = intdiv($totalDays, $count);
        $remainder = $totalDays % $count;
        $cursor = $firstDay->copy();

        $this->periods = [];

        for ($position = 1; $position <= $count; $position++) {
            $length = $baseLength + ($position <= $remainder ? 1 : 0);
            $periodEndsOn = $cursor->copy()->addDays($length - 1);
            $label = $type === AcademicPeriodType::Other ? 'Period '.$position : $type->label().' '.$position;

            $this->periods[] = [
                'name' => $label,
                'type' => $type->value,
                'starts_on' => $cursor->toDateString(),
                'ends_on' => $periodEndsOn->toDateString(),
            ];

            $cursor = $periodEndsOn->copy()->addDay();
        }
    }

    public function addPeriod(): void
    {
        $this->periods[] = [
            'name' => 'Period '.(count($this->periods) + 1),
            'type' => AcademicPeriodType::Other->value,
            'starts_on' => $this->startsOn,
            'ends_on' => $this->endsOn,
        ];
        $this->structure = 'custom';
    }

    public function removePeriod(int $index): void
    {
        if (count($this->periods) === 1) {
            return;
        }

        unset($this->periods[$index]);
        $this->periods = array_values($this->periods);
        $this->structure = 'custom';
    }

    public function save(SaveAcademicCalendar $saveAcademicCalendar): void
    {
        $this->authorizeCalendarChange();
        $validated = $this->validate();

        try {
            $calendar = $saveAcademicCalendar->save(
                school_context()->schoolOrFail(),
                Carbon::parse($validated['startsOn']),
                Carbon::parse($validated['endsOn']),
                $validated['periods'],
                auth()->user(),
                $this->academicYear,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('startsOn', $exception->getMessage());

            return;
        }

        $this->redirectRoute('academic-years.show', $calendar);
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'startsOn' => ['required', 'date'],
            'endsOn' => ['required', 'date', 'after_or_equal:startsOn'],
            'structure' => ['required', Rule::in(array_keys($this->structures()))],
            'periods' => ['required', 'array', 'min:1', 'max:8'],
            'periods.*.name' => ['required', 'string', 'max:100'],
            'periods.*.type' => ['required', Rule::in(array_map(fn (AcademicPeriodType $type): string => $type->value, $this->periodTypes()))],
            'periods.*.starts_on' => ['required', 'date'],
            'periods.*.ends_on' => ['required', 'date', 'after_or_equal:periods.*.starts_on'],
        ];
    }

    /** @return array<string, array<int, mixed>> */
    private function dateRules(): array
    {
        return [
            'startsOn' => ['required', 'date'],
            'endsOn' => ['required', 'date', 'after_or_equal:startsOn'],
        ];
    }

    /** @return array<string, string> */
    public function structures(): array
    {
        return [
            'three_terms' => 'Three terms',
            'two_semesters' => 'Two semesters',
            'three_trimesters' => 'Three trimesters',
            'four_quarters' => 'Four quarters',
            'custom' => 'Custom',
        ];
    }

    /** @return array<int, AcademicPeriodType> */
    public function periodTypes(): array
    {
        return [
            AcademicPeriodType::Term,
            AcademicPeriodType::Semester,
            AcademicPeriodType::Trimester,
            AcademicPeriodType::Quarter,
            AcademicPeriodType::Other,
        ];
    }

    public function canEdit(): bool
    {
        return $this->academicYear === null || $this->academicYear->status === AcademicPeriodStatus::Draft;
    }

    private function authorizeCalendarChange(): void
    {
        $ability = $this->academicYear === null ? 'create' : 'update';
        abort_unless(auth()->user()?->can($ability, $this->academicYear ?? AcademicYear::class), 403);
        abort_unless($this->canEdit(), 422, 'Only draft school calendars can be edited here.');
    }

    public function render(): View
    {
        return view('livewire.academic-calendar-form');
    }
}
