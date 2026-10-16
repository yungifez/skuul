<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
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
     * @param  array{name?: string, code?: string|null, position?: int, is_group?: bool}  $details
     *
     * @throws InvalidValueException when the parent does not fit
     */
    public function update(
        AcademicLevel $academicLevel,
        array $details = [],
        ?AcademicLevel $parent = null,
        ?User $actor = null,
    ): AcademicLevel {
        $isGroup = filter_var($details['is_group'] ?? $academicLevel->is_group, FILTER_VALIDATE_BOOLEAN);

        $this->failIfRecordsDoNotFit($academicLevel, $parent, $isGroup);

        return DB::transaction(function () use ($academicLevel, $details, $parent, $actor, $isGroup): AcademicLevel {
            /** @var AcademicLevel $academicLevel */
            $academicLevel = AcademicLevel::query()->lockForUpdate()->findOrFail($academicLevel->id);

            if ($academicLevel->status === AcademicStructureStatus::Archived) {
                throw new InvalidValueException('An archived '.strtolower(school_term('class_level', 'class')).' cannot be edited.');
            }

            $before = [
                'name' => $academicLevel->name,
                'code' => $academicLevel->code,
                'position' => $academicLevel->position,
                'parent_id' => $academicLevel->parent_id,
                'is_group' => $academicLevel->is_group,
            ];

            $academicLevel->fill([
                'name' => $details['name'] ?? $academicLevel->name,
                'code' => $details['code'] ?? null,
                'position' => $details['position'] ?? 0,
                'parent_id' => $parent?->id,
                'is_group' => $isGroup,
            ]);

            $after = [
                'name' => $academicLevel->name,
                'code' => $academicLevel->code,
                'position' => $academicLevel->position,
                'parent_id' => $academicLevel->parent_id,
                'is_group' => $academicLevel->is_group,
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
        bool $isGroup,
    ): void {
        if ($isGroup && $parent !== null) {
            throw new InvalidValueException('A level group must be a top-level group without a parent.');
        }

        if ($isGroup && !$academicLevel->is_group && $academicLevel->cycleSections()->exists()) {
            throw new InvalidValueException('This level has sections, so it cannot become a level group.');
        }

        if ($isGroup && !$academicLevel->is_group && CourseOffering::query()
            ->whereBelongsTo($academicLevel)
            ->where('roster_mode', '!=', RosterMode::AcademicLevel)
            ->exists()) {
            throw new InvalidValueException('This level has subjects taught by section or to named learners, so it cannot become a level group.');
        }

        if (!$isGroup && $academicLevel->children()->exists()) {
            throw new InvalidValueException('Move the child levels first before marking this group as a teachable level.');
        }

        if ($parent !== null) {
            if ($parent->school_id !== $academicLevel->school_id) {
                throw new InvalidValueException('That level group belongs to another school.');
            }

            if ($parent->id === $academicLevel->id) {
                throw new InvalidValueException('A level cannot sit under itself.');
            }

            if ($this->descendsFrom($parent, $academicLevel)) {
                throw new InvalidValueException('That level group sits under this one already.');
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
