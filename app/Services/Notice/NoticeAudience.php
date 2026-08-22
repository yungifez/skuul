<?php

namespace App\Services\Notice;

use App\Models\AcademicCycleSection;
use App\Models\Notice;
use App\Models\ParentRecord;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Work out who a notice is for.
 *
 * The audience is stored as plain keys on the notice. Current home sections
 * and enrollment records are the learner targets; guardian delivery is an
 * explicit choice, never an accidental side effect of a staff audience.
 */
class NoticeAudience
{
    /**
     * Get the people the notice should reach.
     *
     * An empty audience means active staff and active learners in the school.
     *
     * @return Collection<int, User>
     */
    public function resolve(Notice $notice): Collection
    {
        $audience = $notice->audience ?? [];
        $schoolId = $notice->school_id;

        $query = User::query()->whereKey($this->recipientIds($audience, $schoolId));

        if (!empty($audience['roles'])) {
            $roles = (array) $audience['roles'];
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', $roles));
        }

        return $query->get();
    }

    /**
     * Get the people named by current academic structure or by hand.
     *
     * @param array<string, mixed> $audience
     *
     * @return array<int, int>
     */
    private function recipientIds(array $audience, ?int $schoolId): array
    {
        $allStudentIds = StudentRecord::query()
            ->inSchool($schoolId)
            ->attending()
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $staffIds = User::query()->ofSchool($schoolId)->pluck('id')->all();
        $namedIds = array_map('intval', (array) ($audience['user_ids'] ?? []));
        $hasLearnerTarget = !empty($audience['academic_cycle_section_ids']) || !empty($audience['student_record_ids']);
        $hasNamedTarget = $namedIds !== [];

        if ($hasLearnerTarget || $hasNamedTarget) {
            $studentIds = $this->studentUserIds($audience, $schoolId);
            $ids = array_merge($studentIds, array_intersect($namedIds, array_merge($staffIds, $allStudentIds)));
        } else {
            $studentIds = $allStudentIds;
            $ids = array_merge($staffIds, $studentIds);
        }

        if ((bool) ($audience['include_guardians'] ?? false)) {
            $ids = array_merge($ids, $this->guardianIds($studentIds));
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * Get learners chosen directly or through their current home group.
     *
     * @param array<string, mixed> $audience
     *
     * @return array<int, int>
     */
    private function studentUserIds(array $audience, ?int $schoolId): array
    {
        $sectionIds = array_map('intval', (array) ($audience['academic_cycle_section_ids'] ?? []));
        $studentRecordIds = array_map('intval', (array) ($audience['student_record_ids'] ?? []));

        return StudentRecord::query()
            ->inSchool($schoolId)
            ->attending()
            ->whereNotNull('user_id')
            ->where(function ($query) use ($sectionIds, $studentRecordIds, $schoolId): void {
                if ($sectionIds !== []) {
                    $query->whereIn('academic_cycle_section_id', $this->sectionIdsInSchool($sectionIds, $schoolId));
                }

                if ($studentRecordIds !== []) {
                    if ($sectionIds === []) {
                        $query->whereKey($studentRecordIds);
                    } else {
                        $query->orWhereIn($query->qualifyColumn('id'), $studentRecordIds);
                    }
                }
            })
            ->pluck('user_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * Keep section ids in the notice's school even when this is a queued run.
     *
     * @param array<int, int> $sectionIds
     *
     * @return array<int, int>
     */
    private function sectionIdsInSchool(array $sectionIds, ?int $schoolId): array
    {
        return AcademicCycleSection::query()
            ->inSchool($schoolId)
            ->whereKey($sectionIds)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * Get the people recorded as guardians of the selected learners.
     *
     * @param array<int, int> $studentUserIds
     *
     * @return array<int, int>
     */
    private function guardianIds(array $studentUserIds): array
    {
        if ($studentUserIds === []) {
            return [];
        }

        return ParentRecord::query()
            ->whereHas('students', fn ($query) => $query->whereKey($studentUserIds))
            ->pluck('user_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }
}
