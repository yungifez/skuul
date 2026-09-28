<?php

namespace App\Livewire;

use App\Actions\Discipline\ReportIncident;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Exceptions\InvalidValueException;
use App\Models\Incident;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Record one case and the learners it involves.
 */
class CreateIncident extends Component
{
    use ListsSchoolPeople;

    private const MaxParticipants = 20;

    public string $summary = '';

    public string $category = '';

    public string $occurredAt = '';

    public string $location = '';

    public ?int $assignedTo = null;

    public string $description = '';

    /** @var array<int, array{student_record_id: int|string|null, role: string, note: string}> */
    public array $participants = [];

    public function mount(): void
    {
        Gate::authorize('create', Incident::class);

        $this->category = IncidentCategory::cases()[0]->value;
        $this->occurredAt = now()->format('Y-m-d\TH:i');
        $this->addParticipant();
    }

    public function addParticipant(): void
    {
        if (count($this->participants) < self::MaxParticipants) {
            $this->participants[] = ['student_record_id' => '', 'role' => IncidentParticipantRole::Subject->value, 'note' => ''];
        }
    }

    public function removeParticipant(int $index): void
    {
        unset($this->participants[$index]);
        $this->participants = array_values($this->participants);
    }

    public function save(ReportIncident $reportIncident): void
    {
        Gate::authorize('create', Incident::class);

        $this->validate([
            'summary' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(IncidentCategory::class)],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'occurredAt' => ['required', 'date', 'before_or_equal:now'],
            'assignedTo' => ['nullable', 'integer', Rule::in($this->schoolWorkers()->modelKeys())],
            'participants' => ['array', 'max:'.self::MaxParticipants],
            'participants.*.student_record_id' => ['nullable', 'integer', Rule::exists('student_records', 'id')->where('school_id', current_school_id())],
            'participants.*.role' => ['required', Rule::enum(IncidentParticipantRole::class)],
            'participants.*.note' => ['nullable', 'string', 'max:255'],
        ], [
            'occurredAt.before_or_equal' => 'A case cannot be recorded for a time that has not happened.',
            'assignedTo.in' => 'Choose somebody who works in this school.',
        ]);

        try {
            $incident = $reportIncident->report(
                summary: $this->summary,
                category: IncidentCategory::from($this->category),
                description: $this->description === '' ? null : $this->description,
                occurredAt: $this->occurredAt,
                participants: $this->namedParticipants(),
                reporter: auth()->user(),
                assignee: $this->assignedTo === null ? null : User::findOrFail($this->assignedTo),
                location: $this->location === '' ? null : $this->location,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('summary', $exception->getMessage());

            return;
        }

        session()->flash('success', "Case {$incident->reference} was recorded.");
        $this->redirectRoute('incidents.show', $incident);
    }

    public function render(): View
    {
        return view('livewire.create-incident', [
            'categories' => IncidentCategory::cases(),
            'isRestricted' => IncidentCategory::tryFrom($this->category)?->isRestricted() ?? false,
            'roles' => IncidentParticipantRole::cases(),
            'students' => $this->schoolLearners(),
            'staff' => $this->schoolWorkers(),
            'canAddParticipant' => count($this->participants) < self::MaxParticipants,
        ]);
    }

    /**
     * Keep the rows that name a learner. A row left blank was simply not used.
     *
     * @return array<int, array{enrollment: int, role: IncidentParticipantRole, note: string|null}>
     */
    private function namedParticipants(): array
    {
        return collect($this->participants)
            ->filter(fn (array $row): bool => filled($row['student_record_id']))
            ->map(fn (array $row): array => [
                'enrollment' => (int) $row['student_record_id'],
                'role' => IncidentParticipantRole::from($row['role']),
                'note' => $row['note'] === '' ? null : $row['note'],
            ])
            ->values()
            ->all();
    }
}
