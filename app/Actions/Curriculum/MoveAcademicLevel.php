<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\AcademicLevel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MoveAcademicLevel
{
    /**
     * Move a level within the other levels that share its parent.
     */
    public function __construct(private RecordAuditEvent $auditor) {}

    public function move(AcademicLevel $academicLevel, string $direction, ?User $actor = null): bool
    {
        return DB::transaction(function () use ($academicLevel, $direction, $actor): bool {
            /** @var Collection<int, AcademicLevel> $siblings */
            $siblings = AcademicLevel::query()
                ->where('school_id', $academicLevel->school_id)
                ->where('parent_id', $academicLevel->parent_id)
                ->orderBy('position')
                ->orderBy('name')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $currentIndex = $siblings->search(fn (AcademicLevel $sibling): bool => $sibling->is($academicLevel));

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
                    AuditAction::AcademicLevelUpdated,
                    $sibling,
                    ['position' => ['from' => $from, 'to' => $position]],
                    $actor,
                );
            }

            return $changed;
        });
    }
}
