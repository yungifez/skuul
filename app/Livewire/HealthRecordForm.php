<?php

namespace App\Livewire;

use App\Actions\Wellbeing\RecordHealthInformation;
use App\Exceptions\StaleRecordException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The health facts a school keeps about one child.
 *
 * Only the fields this person changed are saved. A field somebody else
 * changed meanwhile is shown with its new wording and saved over only when
 * this person saves again.
 */
class HealthRecordForm extends Component
{
    use DispatchesStatusNotifications;

    /**
     * @var array<string, string>
     */
    public const array LABELS = [
        'blood_group' => 'Blood group',
        'emergency_contact_name' => 'Emergency contact',
        'emergency_contact_phone' => 'Contact number',
        'emergency_contact_relationship' => 'Relation to the child',
        'conditions' => 'Conditions',
        'allergies' => 'Allergies',
        'medications' => 'Medication',
        'dietary_needs' => 'Food the child cannot eat',
        'notes' => 'Anything else',
    ];

    #[Locked]
    public StudentRecord $enrollment;

    /** @var array<string, string> */
    public array $values = [];

    /** @var array<string, string> */
    #[Locked]
    public array $original = [];

    public function mount(StudentRecord $enrollment): void
    {
        Gate::authorize('viewAny', StudentHealthRecord::class);
        abort_unless($enrollment->school_id === current_school_id(), 404);

        $this->enrollment = $enrollment;
        $this->takeIn($enrollment->healthRecord()->first());
    }

    public function save(RecordHealthInformation $recordHealthInformation): void
    {
        Gate::authorize('create', StudentHealthRecord::class);

        // A form left open can outlive a campus move. The record then belongs
        // to the campus the child attends now.
        abort_unless($this->enrollment->school_id === current_school_id(), 404);

        $this->validate([
            'values.blood_group' => ['nullable', 'string', 'max:10'],
            'values.conditions' => ['nullable', 'string', 'max:2000'],
            'values.allergies' => ['nullable', 'string', 'max:2000'],
            'values.medications' => ['nullable', 'string', 'max:2000'],
            'values.dietary_needs' => ['nullable', 'string', 'max:2000'],
            'values.emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'values.emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'values.emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'values.notes' => ['nullable', 'string', 'max:5000'],
        ], [], collect(self::LABELS)->mapWithKeys(fn (string $label, string $field): array => ["values.{$field}" => str_replace('_', ' ', $field)])->all());

        $changed = array_filter(
            $this->values,
            fn (string $value, string $field): bool => trim($value) !== trim($this->original[$field] ?? ''),
            ARRAY_FILTER_USE_BOTH,
        );

        if ($changed === []) {
            $this->notify('Nothing changed.');

            return;
        }

        try {
            $record = $recordHealthInformation->record(
                $this->enrollment,
                array_map('trim', $changed),
                auth()->user(),
                array_map(fn (string $value): ?string => trim($value) === '' ? null : trim($value), array_intersect_key($this->original, $changed)),
            );
        } catch (StaleRecordException $exception) {
            $current = $this->enrollment->healthRecord()->first();

            foreach ($exception->fields() as $field) {
                $now = (string) $current?->{$field};
                $this->original[$field] = $now;
                $this->addError("values.{$field}", 'Somebody else changed this while you worked. It now says: "'.($now === '' ? '—' : $now).'". Save again to keep yours.');
            }

            return;
        }

        $this->takeIn($record->fresh());
        $this->notify('The health record was saved.');
    }

    public function render(): View
    {
        return view('livewire.health-record-form', [
            'record' => $this->enrollment->healthRecord()->with('updatedBy:id,name')->first(),
            'canWrite' => Gate::allows('create', StudentHealthRecord::class),
        ]);
    }

    /**
     * Show the record as it is saved now.
     */
    private function takeIn(?StudentHealthRecord $record): void
    {
        foreach (array_keys(self::LABELS) as $field) {
            $this->values[$field] = (string) $record?->{$field};
        }

        $this->original = $this->values;
    }
}
