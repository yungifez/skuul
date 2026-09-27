<?php

namespace App\Livewire;

use App\Actions\Gradebook\SaveGradingScale;
use App\Enums\GradingScaleType;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\GradingScale;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The grading scales a school's teachers pick from when they mark work.
 */
class GradingScaleManager extends Component
{
    use DispatchesStatusNotifications;

    public bool $isEditing = false;

    #[Locked]
    public ?int $editingId = null;

    #[Locked]
    public ?string $readAt = null;

    /** @var list<int> */
    #[Locked]
    public array $recordedOptionIds = [];

    public string $name = '';

    public string $description = '';

    public string $scaleType = '';

    public string $maximumValue = '4.0';

    public bool $isActive = true;

    /** @var list<array{id: int|null, label: string, points: string}> */
    public array $options = [];

    public function mount(): void
    {
        Gate::authorize('viewAny', GradingScale::class);
    }

    public function startCreating(): void
    {
        Gate::authorize('create', GradingScale::class);

        $this->stopEditing();
        $this->isEditing = true;
        $this->scaleType = GradingScaleType::Percentage->value;
        $this->options = array_fill(0, 3, ['id' => null, 'label' => '', 'points' => '']);
    }

    public function startChanging(int $scaleId, SaveGradingScale $saveGradingScale): void
    {
        $scale = $this->scale($scaleId);
        Gate::authorize('update', $scale);

        $this->stopEditing();
        $this->isEditing = true;
        $this->editingId = $scale->id;
        $this->readAt = $scale->updated_at?->toIso8601String();
        $this->recordedOptionIds = $saveGradingScale->recordedOptionIds($scale);
        $this->name = $scale->name;
        $this->description = (string) $scale->description;
        $this->scaleType = $scale->scale_type->value;
        $this->maximumValue = $scale->maximum_value === null ? '4.0' : (string) $scale->maximum_value;
        $this->isActive = $scale->is_active;
        $this->options = $scale->options->map(fn ($option): array => [
            'id' => $option->id,
            'label' => $option->label,
            'points' => $option->points === null ? '' : (string) $option->points,
        ])->values()->all();
    }

    public function stopEditing(): void
    {
        $this->reset('isEditing', 'editingId', 'readAt', 'recordedOptionIds', 'name', 'description', 'scaleType', 'maximumValue', 'isActive', 'options');
        $this->resetValidation();
    }

    public function addOption(): void
    {
        $this->options[] = ['id' => null, 'label' => '', 'points' => ''];
    }

    public function removeOption(int $index): void
    {
        $id = $this->options[$index]['id'] ?? null;

        if ($id !== null && in_array($id, $this->recordedOptionIds, true)) {
            $this->addError('options', 'A grade option already used in a learner record cannot be removed.');

            return;
        }

        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function save(SaveGradingScale $saveGradingScale): void
    {
        $scale = $this->editingId === null ? null : $this->scale($this->editingId);

        if ($scale === null) {
            Gate::authorize('create', GradingScale::class);
        } else {
            Gate::authorize('update', $scale);
        }

        $isGpa = $this->scaleType === GradingScaleType::Gpa->value;

        $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('grading_scales', 'name')->where('school_id', current_school_id())->ignore($scale?->id)],
            'description' => ['nullable', 'string', 'max:5000'],
            'scaleType' => ['required', Rule::enum(GradingScaleType::class)],
            'maximumValue' => [Rule::requiredIf($isGpa), 'nullable', 'numeric', 'gt:0', 'max:999999.99'],
            'options' => ['required', 'array', 'max:50'],
            'options.*.label' => ['nullable', 'string', 'max:100'],
            'options.*.points' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ], [
            'name.unique' => 'The school already has a scale with that name.',
        ], [
            'scaleType' => 'scale basis',
            'maximumValue' => 'maximum GPA',
            'options.*.label' => 'grade option',
            'options.*.points' => 'value',
        ]);

        $attributes = [
            'name' => trim($this->name),
            'description' => trim($this->description) === '' ? null : trim($this->description),
            'scale_type' => $this->scaleType,
            'maximum_value' => $isGpa ? (float) $this->maximumValue : null,
            'is_active' => $this->isActive,
            'options' => array_map(fn (array $option): array => [
                'id' => $option['id'],
                'label' => $option['label'],
                'points' => $option['points'] === '' ? null : $option['points'],
            ], $this->options),
        ];

        try {
            $scale === null
                ? $saveGradingScale->create($attributes, auth()->user())
                : $saveGradingScale->update($scale, $attributes, auth()->user(), $this->readAt);
        } catch (InvalidValueException $exception) {
            $this->addError('options', $exception->getMessage());

            return;
        }

        $this->stopEditing();
        $this->notify($scale === null ? "{$attributes['name']} is ready for teachers." : 'Saved.');
    }

    public function setOffered(int $scaleId, bool $isOffered): void
    {
        $scale = $this->scale($scaleId);
        Gate::authorize('update', $scale);

        $scale->is_active = $isOffered;
        $scale->save();

        $this->notify($isOffered ? "{$scale->name} is offered for new assessments." : "{$scale->name} is no longer offered for new assessments. Marks already given keep it.");
    }

    public function delete(int $scaleId, SaveGradingScale $saveGradingScale): void
    {
        $scale = $this->scale($scaleId);
        Gate::authorize('delete', $scale);

        try {
            $saveGradingScale->delete($scale, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        if ($this->editingId === $scale->id) {
            $this->stopEditing();
        }

        $this->notify("{$scale->name} was deleted.");
    }

    public function render(): View
    {
        return view('livewire.grading-scale-manager', [
            'scales' => GradingScale::query()
                ->inSchool()
                ->with('options')
                ->withCount('gradeItems')
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
            'scaleTypes' => GradingScaleType::cases(),
        ]);
    }

    private function scale(int $scaleId): GradingScale
    {
        return GradingScale::query()->inSchool()->with('options')->findOrFail($scaleId);
    }
}
