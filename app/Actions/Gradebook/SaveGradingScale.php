<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\GradingScaleType;
use App\Exceptions\InvalidValueException;
use App\Models\GradeEntry;
use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaveGradingScale
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Create a reusable school-owned grading scale.
     *
     * @param  array{name: string, description?: string|null, scale_type?: string, maximum_value?: float|int|string|null, is_active?: bool, options: array<int, array{id?: int|null, label?: string|null, points?: float|int|string|null}>}  $attributes
     *
     * @throws InvalidValueException when the options do not make a usable scale
     */
    public function create(array $attributes, User $actor): GradingScale
    {
        $this->refuseUnusableOptions($attributes);

        return DB::transaction(function () use ($attributes, $actor): GradingScale {
            $scale = GradingScale::create(Arr::except($attributes, 'options') + [
                'school_id' => current_school_id(),
                'created_by' => $actor->id,
            ]);

            $this->syncOptions($scale, $attributes['options']);
            $this->audit->record(AuditAction::GradingScaleSaved, $scale, ['created' => true], $actor);

            return $scale;
        });
    }

    /**
     * Update a scale without changing any option already used in a grade.
     *
     * A caller that read the scale earlier passes the time it was last saved.
     * When somebody else saved it since, nothing is written, so their new
     * options are never quietly removed.
     *
     * @param  array{name: string, description?: string|null, scale_type?: string, maximum_value?: float|int|string|null, is_active?: bool, options: array<int, array{id?: int|null, label?: string|null, points?: float|int|string|null}>}  $attributes
     *
     * @throws InvalidValueException when the change would rewrite a recorded grade or somebody else saved first
     */
    public function update(GradingScale $scale, array $attributes, User $actor, ?string $readAt = null): GradingScale
    {
        $this->refuseUnusableOptions($attributes);

        return DB::transaction(function () use ($scale, $attributes, $actor, $readAt): GradingScale {
            $scale = GradingScale::query()->lockForUpdate()->findOrFail($scale->getKey());

            if ($readAt !== null && $scale->updated_at?->toIso8601String() !== $readAt) {
                throw new InvalidValueException('Somebody else saved this scale while you worked on it. Open it again to see their changes.');
            }

            $scaleType = (string) ($attributes['scale_type'] ?? $scale->scale_type->value);
            $maximumValue = array_key_exists('maximum_value', $attributes)
                ? ($attributes['maximum_value'] === null ? null : (float) $attributes['maximum_value'])
                : $scale->maximum_value;
            $hasRecordedGrades = GradeEntry::query()
                ->whereHas('gradingScaleOption', fn (Builder $query): Builder => $query->where('grading_scale_id', $scale->id))
                ->exists();

            if ($hasRecordedGrades && ($scaleType !== $scale->scale_type->value || $maximumValue !== $scale->maximum_value)) {
                throw new InvalidValueException('A grading scale used in learner records cannot change its basis or maximum value. Create a new scale instead.');
            }

            $scale->fill(Arr::except($attributes, 'options'));
            $scale->updated_at = now();
            $scale->save();
            $this->syncOptions($scale, $attributes['options']);
            $this->audit->record(AuditAction::GradingScaleSaved, $scale, ['created' => false], $actor);

            return $scale;
        });
    }

    /**
     * Delete a scale no assessment uses.
     *
     * @throws InvalidValueException when an assessment uses the scale
     */
    public function delete(GradingScale $scale, User $actor): void
    {
        DB::transaction(function () use ($scale, $actor): void {
            $scale = GradingScale::query()->lockForUpdate()->findOrFail($scale->getKey());

            if ($scale->gradeItems()->exists()) {
                throw new InvalidValueException('This scale is used by an assessment. Stop offering it instead of deleting it.');
            }

            $scale->delete();
            $this->audit->record(AuditAction::GradingScaleDeleted, $scale, [], $actor);
        });
    }

    /**
     * Get the options already used in a learner record.
     *
     * @return list<int>
     */
    public function recordedOptionIds(GradingScale $scale): array
    {
        return GradeEntry::query()
            ->whereIn('grading_scale_option_id', $scale->options()->select('id'))
            ->distinct()
            ->pluck('grading_scale_option_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->values()
            ->all();
    }

    /**
     * Refuse options that would not make a scale teachers can mark with.
     *
     * @param  array{scale_type?: string, maximum_value?: float|int|string|null, options: array<int, array{id?: int|null, label?: string|null, points?: float|int|string|null}>}  $attributes
     *
     * @throws InvalidValueException
     */
    private function refuseUnusableOptions(array $attributes): void
    {
        /** @var Collection<int, array{id?: int|null, label?: string|null, points?: float|int|string|null}> $options */
        $options = collect($attributes['options'])
            ->filter(fn (array $option): bool => trim((string) ($option['label'] ?? '')) !== '');

        if ($options->count() < 2) {
            throw new InvalidValueException('Give the scale at least two grade options.');
        }

        if ($options->map(fn (array $option): string => mb_strtolower(trim((string) $option['label'])))->unique()->count() !== $options->count()) {
            throw new InvalidValueException('Each grade option needs a different label.');
        }

        $scaleType = GradingScaleType::tryFrom((string) ($attributes['scale_type'] ?? ''));

        if ($scaleType === null) {
            return;
        }

        $withPoints = $options->filter(fn (array $option): bool => isset($option['points']) && $option['points'] !== '');

        match (true) {
            $scaleType === GradingScaleType::Descriptive && $withPoints->isNotEmpty() => throw new InvalidValueException('A descriptive scale leaves the values blank.'),
            in_array($scaleType, [GradingScaleType::Percentage, GradingScaleType::Gpa], true) && $withPoints->count() !== $options->count() => throw new InvalidValueException('Give every grade option a value.'),
            $scaleType === GradingScaleType::Points && $withPoints->isNotEmpty() && $withPoints->count() !== $options->count() => throw new InvalidValueException('Give every grade option points, or leave points blank for all of them.'),
            $scaleType === GradingScaleType::Percentage && $withPoints->contains(fn (array $option): bool => (float) $option['points'] > 100) => throw new InvalidValueException('A percentage is between 0 and 100.'),
            $scaleType === GradingScaleType::Gpa && ($attributes['maximum_value'] ?? null) !== null && $withPoints->contains(fn (array $option): bool => (float) $option['points'] > (float) $attributes['maximum_value']) => throw new InvalidValueException('A GPA value cannot be higher than the maximum GPA.'),
            default => null,
        };
    }

    /**
     * Keep unused options editable, but protect options that already describe a recorded grade.
     *
     * @param  array<int, array{id?: int|null, label?: string|null, points?: float|int|string|null}>  $options
     */
    private function syncOptions(GradingScale $scale, array $options): void
    {
        $existingOptions = $scale->options()->get()->keyBy('id');
        $submittedIds = [];
        $position = 1;

        foreach ($options as $optionAttributes) {
            $label = trim((string) ($optionAttributes['label'] ?? ''));

            if ($label === '') {
                continue;
            }

            $optionId = $optionAttributes['id'] ?? null;
            $points = ($optionAttributes['points'] ?? null) === null || $optionAttributes['points'] === ''
                ? null
                : (float) $optionAttributes['points'];

            if ($optionId === null) {
                $scale->options()->create([
                    'label' => $label,
                    'points' => $points,
                    'position' => $position++,
                ]);

                continue;
            }

            $option = $existingOptions->get((int) $optionId);

            if (!$option instanceof GradingScaleOption) {
                throw new InvalidValueException('A grading-scale option does not belong to this scale.');
            }

            $submittedIds[] = $option->id;
            $isRecorded = GradeEntry::query()->whereBelongsTo($option, 'gradingScaleOption')->exists();

            if ($isRecorded && ($option->label !== $label || $option->points !== $points)) {
                throw new InvalidValueException('A grade option already used in a learner record cannot be changed. Add a new option instead.');
            }

            $option->update(['label' => $label, 'points' => $points, 'position' => $position++]);
        }

        $removedOptions = $existingOptions->except($submittedIds);

        foreach ($removedOptions as $option) {
            if (GradeEntry::query()->whereBelongsTo($option, 'gradingScaleOption')->exists()) {
                throw new InvalidValueException('A grade option already used in a learner record cannot be removed.');
            }

            $option->delete();
        }
    }
}
