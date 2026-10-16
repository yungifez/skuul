<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Models\AcademicCycleSection;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MoveAcademicCycleSection
{
    /**
     * Move a section within the other sections of its level and year.
     */
    public function __construct(private RecordAuditEvent $auditor) {}

    public function move(AcademicCycleSection $section, string $direction, ?User $actor = null): bool
    {
        return DB::transaction(function () use ($section, $direction, $actor): bool {
            /** @var AcademicCycleSection $section */
            $section = AcademicCycleSection::query()
                ->with('academicYear')
                ->lockForUpdate()
                ->findOrFail($section->id);

            if ($section->status === AcademicStructureStatus::Archived || $section->academicYear->isClosed()) {
                return false;
            }

            /** @var Collection<int, AcademicCycleSection> $siblings */
            $siblings = AcademicCycleSection::query()
                ->where('school_id', $section->school_id)
                ->where('academic_year_id', $section->academic_year_id)
                ->where('academic_level_id', $section->academic_level_id)
                ->orderBy('position')
                ->orderBy('name')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $currentIndex = $siblings->search(fn (AcademicCycleSection $sibling): bool => $sibling->is($section));

            if ($currentIndex === false) {
                return false;
            }

            $swapIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

            if (!isset($siblings[$swapIndex])) {
                return false;
            }

            $ordered = $siblings->all();
            [$ordered[$currentIndex], $ordered[$swapIndex]] = [$ordered[$swapIndex], $ordered[$currentIndex]];

            $changed = false;

            foreach ($ordered as $position => $sibling) {
                if ($sibling->position === $position) {
                    continue;
                }

                $from = $sibling->position;
                $sibling->position = $position;
                $sibling->save();
                $changed = true;

                $this->auditor->record(
                    AuditAction::AcademicCycleSectionUpdated,
                    $sibling,
                    ['position' => ['from' => $from, 'to' => $position]],
                    $actor,
                );
            }

            return $changed;
        });
    }
}
