<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RollForwardAcademicCycleSections
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Read what a roll-forward would do, without writing anything.
     *
     * The screen shows this before the person confirms, so the copy is never
     * a surprise. `copies` are the sections that would be created as drafts.
     * `skips` are the source sections whose name already exists in the target
     * cycle, which is what makes a repeated confirmation safe.
     *
     * @throws InvalidValueException when the source and target cannot share a structure
     *
     * @return array{copies: Collection<int, AcademicCycleSection>, skips: Collection<int, AcademicCycleSection>}
     */
    public function preview(AcademicYear $source, AcademicYear $target): array
    {
        $this->failIfCyclesDoNotFit($source, $target);

        /** @var Collection<int, AcademicCycleSection> $copies */
        $copies = new Collection();
        /** @var Collection<int, AcademicCycleSection> $skips */
        $skips = new Collection();

        foreach ($this->sectionsOf($source) as $sourceSection) {
            if ($this->targetAlreadyHas($target, $sourceSection)) {
                $skips->push($sourceSection);

                continue;
            }

            $copies->push($sourceSection);
        }

        return ['copies' => $copies, 'skips' => $skips];
    }

    /**
     * Copy the reusable structure into the next cycle as drafts.
     *
     * Learner placement, old section links, teacher assignments, and academic
     * work do not come forward. They must be deliberately set for the target
     * cycle.
     *
     * @throws InvalidValueException when the source and target cannot share a structure
     *
     * @return Collection<int, AcademicCycleSection>
     */
    public function rollForward(AcademicYear $source, AcademicYear $target, ?User $actor = null): Collection
    {
        $this->failIfCyclesDoNotFit($source, $target);

        return DB::transaction(function () use ($source, $target, $actor): Collection {
            $target = AcademicYear::inSchool()->whereKey($target->id)->lockForUpdate()->firstOrFail();

            if ($target->isClosed()) {
                throw new InvalidValueException('The target academic cycle is closed.');
            }

            /** @var Collection<int, AcademicCycleSection> $created */
            $created = new Collection();

            foreach ($this->sectionsOf($source) as $sourceSection) {
                if ($this->targetAlreadyHas($target, $sourceSection)) {
                    continue;
                }

                $created->push(AcademicCycleSection::create([
                    'school_id'         => $target->school_id,
                    'academic_year_id'  => $target->id,
                    'academic_level_id' => $sourceSection->academic_level_id,
                    'name'              => $sourceSection->name,
                    'label'             => $sourceSection->label,
                    'stream'            => $sourceSection->stream,
                    'shift'             => $sourceSection->shift,
                    'language'          => $sourceSection->language,
                    'room'              => $sourceSection->room,
                    'capacity'          => $sourceSection->capacity,
                    'position'          => $sourceSection->position,
                    'status'            => AcademicStructureStatus::Draft,
                ]));
            }

            if ($created->isNotEmpty()) {
                $this->auditor->record(
                    AuditAction::AcademicCycleSectionsRolledForward,
                    $target,
                    [
                        'source_academic_year_id'    => $source->id,
                        'target_academic_year_id'    => $target->id,
                        'academic_cycle_section_ids' => $created->modelKeys(),
                    ],
                    $actor,
                );
            }

            return $created;
        });
    }

    /**
     * @return Collection<int, AcademicCycleSection>
     */
    private function sectionsOf(AcademicYear $source): Collection
    {
        return AcademicCycleSection::inSchool()
            ->with('academicLevel:id,name,label')
            ->whereBelongsTo($source, 'academicYear')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    private function targetAlreadyHas(AcademicYear $target, AcademicCycleSection $sourceSection): bool
    {
        return AcademicCycleSection::inSchool()
            ->whereBelongsTo($target, 'academicYear')
            ->where('academic_level_id', $sourceSection->academic_level_id)
            ->where('name', $sourceSection->name)
            ->exists();
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfCyclesDoNotFit(AcademicYear $source, AcademicYear $target): void
    {
        if ($source->school_id !== $target->school_id) {
            throw new InvalidValueException('Academic cycles from different schools cannot share sections.');
        }

        if ($source->id === $target->id) {
            throw new InvalidValueException('Choose a different academic cycle to receive the sections.');
        }
    }
}
