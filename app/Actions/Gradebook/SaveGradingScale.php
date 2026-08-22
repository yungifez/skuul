<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\GradeEntry;
use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SaveGradingScale
{
    public function __construct(private RecordAuditEvent $audit)
    {
    }

    /**
     * Create a reusable school-owned grading scale.
     *
     * @param array{name: string, description?: string|null, is_active?: bool, options: array<int, array{id?: int, label?: string|null, points?: float|int|string|null}>} $attributes
     */
    public function create(array $attributes, User $actor): GradingScale
    {
        return DB::transaction(function () use ($attributes, $actor): GradingScale {
            $scale = GradingScale::create(Arr::except($attributes, 'options') + [
                'school_id'  => current_school_id(),
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
     * @param array{name: string, description?: string|null, is_active?: bool, options: array<int, array{id?: int, label?: string|null, points?: float|int|string|null}>} $attributes
     */
    public function update(GradingScale $scale, array $attributes, User $actor): GradingScale
    {
        return DB::transaction(function () use ($scale, $attributes, $actor): GradingScale {
            $scale->update(Arr::except($attributes, 'options'));
            $this->syncOptions($scale, $attributes['options']);
            $this->audit->record(AuditAction::GradingScaleSaved, $scale, ['created' => false], $actor);

            return $scale;
        });
    }

    /**
     * Keep unused options editable, but protect options that already describe a recorded grade.
     *
     * @param array<int, array{id?: int, label?: string|null, points?: float|int|string|null}> $options
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
            $points = $optionAttributes['points'] === null || $optionAttributes['points'] === ''
                ? null
                : (float) $optionAttributes['points'];

            if ($optionId === null) {
                $scale->options()->create([
                    'label'    => $label,
                    'points'   => $points,
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
