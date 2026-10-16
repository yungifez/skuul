<?php

namespace App\Services\Syllabus;

use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\CurriculumOutline;
use App\Models\CurriculumOutlineTopic;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Keep reusable schemes of work, and copy topics into a syllabus draft.
 *
 * Copying from an earlier syllabus is how a plan moves forward into a new
 * term or session.
 */
class CurriculumLibraryService
{
    /**
     * Keep a copy of a syllabus's topics in the school library.
     */
    public function saveFromSyllabus(Syllabus $syllabus, string $name, ?string $description, ?User $actor = null): CurriculumOutline
    {
        $courseOffering = $syllabus->courseOffering;
        $topics = $syllabus->topics()->get();

        if ($topics->isEmpty()) {
            throw new InvalidValueException('Add topics to the syllabus before saving it to the library.');
        }

        return DB::transaction(function () use ($courseOffering, $topics, $name, $description, $actor): CurriculumOutline {
            $outline = CurriculumOutline::create([
                'school_id' => $courseOffering->school_id,
                'subject_id' => $courseOffering->subject_id,
                'academic_level_id' => $courseOffering->academic_level_id,
                'name' => $name,
                'description' => $description,
                'created_by' => ($actor ?? auth()->user())?->id,
            ]);

            foreach ($topics->values() as $position => $topic) {
                $outline->topics()->create($topic->only(['week', 'title', 'objectives', 'content', 'resources']) + ['position' => $position + 1]);
            }

            return $outline;
        });
    }

    /**
     * Get the library outlines a draft can copy from: the same subject, for its level or any level.
     *
     * @return Collection<int, CurriculumOutline>
     */
    public function outlinesFor(Syllabus $syllabus): Collection
    {
        $courseOffering = $syllabus->courseOffering;

        return CurriculumOutline::query()
            ->inSchool($courseOffering->school_id)
            ->where('subject_id', $courseOffering->subject_id)
            ->where(function (Builder $outlines) use ($courseOffering): void {
                $outlines->whereNull('academic_level_id')->orWhere('academic_level_id', $courseOffering->academic_level_id);
            })
            ->withCount('topics')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the earlier syllabi of the same subject that a draft can copy from.
     *
     * @return Collection<int, Syllabus>
     */
    public function earlierSyllabiFor(Syllabus $syllabus): Collection
    {
        $courseOffering = $syllabus->courseOffering;

        return Syllabus::query()
            ->inSchool($courseOffering->school_id)
            ->whereKeyNot($syllabus->id)
            ->whereIn('status', [SyllabusStatus::Published, SyllabusStatus::Superseded])
            ->whereHas('courseOffering', fn (Builder $offerings) => $offerings->where('subject_id', $courseOffering->subject_id))
            ->has('topics')
            ->with(['courseOffering.academicLevel:id,name', 'courseOffering.academicPeriod', 'courseOffering.academicYear:id,start_year,stop_year'])
            ->withCount('topics')
            ->latest('published_at')
            ->limit(25)
            ->get();
    }

    /**
     * Add the topics of an outline or an earlier syllabus to the end of a draft.
     *
     * @return int The number of topics copied.
     */
    public function copyInto(Syllabus $draft, CurriculumOutline|Syllabus $source): int
    {
        if ($draft->fresh()?->status !== SyllabusStatus::Draft) {
            throw new InvalidValueException('Only a draft syllabus can be changed. Create a revised draft instead.');
        }

        $isAllowed = $source instanceof CurriculumOutline
            ? $this->outlinesFor($draft)->contains('id', $source->id)
            : $this->earlierSyllabiFor($draft)->contains('id', $source->id);

        if (!$isAllowed) {
            throw new InvalidValueException('Copy from an outline or syllabus of the same subject in this school.');
        }

        return DB::transaction(function () use ($draft, $source): int {
            $position = (int) SyllabusTopic::query()->where('syllabus_id', $draft->id)->lockForUpdate()->max('position');
            $topics = $source->topics()->get();

            foreach ($topics as $topic) {
                /** @var CurriculumOutlineTopic|SyllabusTopic $topic */
                $draft->topics()->create($topic->only(['week', 'title', 'objectives', 'content', 'resources']) + ['position' => ++$position]);
            }

            return $topics->count();
        });
    }
}
