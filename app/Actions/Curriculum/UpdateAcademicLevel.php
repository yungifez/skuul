<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateAcademicLevel
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Change the reusable setup of a level.
     *
     * The change never touches the sections, placements, or results that
     * already name the level. Only the level's own description moves.
     *
     * @param  array{name?: string, label?: string|null, code?: string|null, position?: int}  $details
     *
     * @throws InvalidValueException when the parent does not fit
     */
    public function update(
        AcademicLevel $academicLevel,
        array $details = [],
        ?AcademicLevel $parent = null,
        ?User $actor = null,
    ): AcademicLevel {
        $this->failIfRecordsDoNotFit($academicLevel, $parent);

        return DB::transaction(function () use ($academicLevel, $details, $parent, $actor): AcademicLevel {
            /** @var AcademicLevel $academicLevel */
            $academicLevel = AcademicLevel::query()->lockForUpdate()->findOrFail($academicLevel->id);

            if ($academicLevel->status === AcademicStructureStatus::Archived) {
                throw new InvalidValueException('An archived academic level cannot be edited.');
            }

            $before = [
                'name' => $academicLevel->name,
                'label' => $academicLevel->label,
                'code' => $academicLevel->code,
                'position' => $academicLevel->position,
                'parent_id' => $academicLevel->parent_id,
            ];

            $academicLevel->fill([
                'name' => $details['name'] ?? $academicLevel->name,
                'label' => $details['label'] ?? null,
                'code' => $details['code'] ?? null,
                'position' => $details['position'] ?? 0,
                'parent_id' => $parent?->id,
            ]);

            $after = [
                'name' => $academicLevel->name,
                'label' => $academicLevel->label,
                'code' => $academicLevel->code,
                'position' => $academicLevel->position,
                'parent_id' => $academicLevel->parent_id,
            ];

            if ($before === $after) {
                return $academicLevel;
            }

            $academicLevel->save();

            $this->auditor->record(
                AuditAction::AcademicLevelUpdated,
                $academicLevel,
                ['from' => $before, 'to' => $after],
                $actor,
            );

            return $academicLevel;
        });
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(
        AcademicLevel $academicLevel,
        ?AcademicLevel $parent,
    ): void {
        if ($parent !== null) {
            if ($parent->school_id !== $academicLevel->school_id) {
                throw new InvalidValueException('The parent academic level belongs to another school.');
            }

            if ($parent->id === $academicLevel->id) {
                throw new InvalidValueException('An academic level cannot be its own parent.');
            }

            if ($this->descendsFrom($parent, $academicLevel)) {
                throw new InvalidValueException('That parent sits under this academic level already.');
            }
        }
    }

    /**
     * Answer whether the candidate parent already sits under the level.
     */
    private function descendsFrom(AcademicLevel $candidate, AcademicLevel $academicLevel): bool
    {
        $seen = [];
        $current = $candidate;

        while ($current->parent_id !== null && !in_array($current->parent_id, $seen, true)) {
            if ($current->parent_id === $academicLevel->id) {
                return true;
            }

            $seen[] = $current->parent_id;
            $parent = AcademicLevel::query()->find($current->parent_id);

            if ($parent === null) {
                return false;
            }

            $current = $parent;
        }

        return false;
    }
}
