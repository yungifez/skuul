<?php

namespace App\Services\Syllabus;

use App\Enums\RosterMode;
use App\Enums\SyllabusStatus;
use App\Enums\TopicCoverageStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Record and measure what each class was actually taught of a syllabus.
 */
class SyllabusCoverageService
{
    /**
     * Get the classes that move through the syllabus at their own pace.
     *
     * An offering for a whole level is taught section by section, so each
     * section is a track. Every other roster is taught as one group.
     *
     * @return list<array{id: int|null, label: string}>
     */
    public function tracks(Syllabus $syllabus): array
    {
        $courseOffering = $syllabus->courseOffering;
        $wholeClass = [['id' => null, 'label' => 'Whole class']];

        if ($courseOffering->roster_mode !== RosterMode::AcademicLevel) {
            return $wholeClass;
        }

        $sections = AcademicCycleSection::query()
            ->inSchool($courseOffering->school_id)
            ->where('academic_year_id', $courseOffering->academic_year_id)
            ->whereIn('academic_level_id', $courseOffering->academicLevel->teachingScopeIds())
            ->with('academicLevel:id,name')
            ->orderBy('academic_level_id')
            ->orderBy('name')
            ->get();

        if ($sections->isEmpty()) {
            return $wholeClass;
        }

        $tracks = [];

        foreach ($sections as $section) {
            $tracks[] = ['id' => $section->id, 'label' => $section->qualifiedName()];
        }

        return $tracks;
    }

    /**
     * Record how far one class was taught a topic. A null status clears the record.
     */
    public function record(
        Syllabus $syllabus,
        SyllabusTopic $topic,
        ?int $academicCycleSectionId,
        ?TopicCoverageStatus $status,
        ?string $coveredOn = null,
        ?string $note = null,
        ?User $actor = null,
    ): ?SyllabusTopicCoverage {
        if ($syllabus->fresh()?->status !== SyllabusStatus::Published) {
            throw new InvalidValueException('Coverage is recorded against the published syllabus only.');
        }

        if ($topic->syllabus_id !== $syllabus->id) {
            throw new InvalidValueException('That topic belongs to another syllabus.');
        }

        if (!in_array($academicCycleSectionId, array_column($this->tracks($syllabus), 'id'), true)) {
            throw new InvalidValueException('That class does not take this course offering.');
        }

        $match = ['syllabus_topic_id' => $topic->id, 'academic_cycle_section_id' => $academicCycleSectionId];

        if ($status === null) {
            SyllabusTopicCoverage::query()->where($match)->first()?->delete();

            return null;
        }

        return SyllabusTopicCoverage::query()->updateOrCreate($match, [
            'status' => $status,
            'covered_on' => $status === TopicCoverageStatus::Skipped ? null : ($coveredOn ?? now()->toDateString()),
            'note' => $note,
            'recorded_by' => ($actor ?? auth()->user())?->id,
        ]);
    }

    /**
     * Get each track's coverage, keyed by topic id.
     *
     * @return Collection<int, SyllabusTopicCoverage>
     */
    public function coverageFor(Syllabus $syllabus, ?int $academicCycleSectionId): Collection
    {
        return SyllabusTopicCoverage::query()
            ->whereIn('syllabus_topic_id', $syllabus->topics()->select('syllabus_topics.id'))
            ->where('academic_cycle_section_id', $academicCycleSectionId)
            ->get()
            ->keyBy('syllabus_topic_id');
    }

    /**
     * Measure each track against the plan.
     *
     * A topic planned for a week before the current one is expected. It is
     * behind when it was neither covered nor deliberately skipped.
     *
     * @return list<array{id: int|null, label: string, total: int, covered: int, partial: int, skipped: int, expected: int, behind: int, percent: int}>
     */
    public function summary(Syllabus $syllabus, ?CarbonInterface $on = null): array
    {
        $topics = $syllabus->topics()->get(['syllabus_topics.id', 'week']);
        $currentWeek = $syllabus->teachingWeekOn($on);
        $expectedIds = $currentWeek === null
            ? []
            : $topics->filter(fn (SyllabusTopic $topic): bool => $topic->week !== null && $topic->week < $currentWeek)->modelKeys();
        $coverages = SyllabusTopicCoverage::query()
            ->whereIn('syllabus_topic_id', $topics->modelKeys())
            ->get(['syllabus_topic_id', 'academic_cycle_section_id', 'status'])
            ->groupBy(fn (SyllabusTopicCoverage $coverage): string => (string) $coverage->academic_cycle_section_id);

        $summary = [];

        foreach ($this->tracks($syllabus) as $track) {
            $records = $coverages->get((string) $track['id']) ?? new Collection;
            $settledIds = $records->filter(fn (SyllabusTopicCoverage $coverage): bool => $coverage->status->isSettled())->pluck('syllabus_topic_id');
            $covered = $records->where('status', TopicCoverageStatus::Covered)->count();
            $total = $topics->count();

            $summary[] = [
                'id' => $track['id'],
                'label' => $track['label'],
                'total' => $total,
                'covered' => $covered,
                'partial' => $records->where('status', TopicCoverageStatus::Partial)->count(),
                'skipped' => $records->where('status', TopicCoverageStatus::Skipped)->count(),
                'expected' => count($expectedIds),
                'behind' => collect($expectedIds)->diff($settledIds)->count(),
                'percent' => $total === 0 ? 0 : (int) round($covered / $total * 100),
            ];
        }

        return $summary;
    }
}
