<?php

namespace App\Http\Controllers;

use App\Enums\CourseOfferingStatus;
use App\Enums\PortalArea;
use App\Enums\SyllabusStatus;
use App\Models\StudentRecord;
use App\Models\Syllabus;
use App\Services\Gradebook\CourseOfferingRoster;
use App\Services\Portal\PortalAccess;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show a learner and their guardian what each course plans to teach, and how far the class is.
 */
class PortalSyllabusController extends Controller
{
    public function __construct(
        private PortalAccess $access,
        private CourseOfferingRoster $roster,
        private SyllabusCoverageService $coverage,
    ) {}

    /**
     * Read the published syllabi of one enrollment's active courses.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Syllabi, $studentRecord->school_id), 404);

        $syllabi = Syllabus::query()
            ->inSchool($studentRecord->school_id)
            ->where('status', SyllabusStatus::Published)
            ->whereHas('courseOffering', fn (Builder $offerings) => $offerings->where('status', CourseOfferingStatus::Active))
            ->with(['courseOffering.subject:id,name', 'courseOffering.academicPeriod', 'courseOffering.academicLevel', 'topics'])
            ->get()
            ->filter(fn (Syllabus $syllabus): bool => $this->roster->includes($syllabus->courseOffering, $studentRecord))
            ->sortBy(fn (Syllabus $syllabus): string => $syllabus->courseOffering->subject->name)
            ->values();

        $progress = $syllabi->mapWithKeys(function (Syllabus $syllabus) use ($studentRecord): array {
            $trackIds = array_column($this->coverage->tracks($syllabus), 'id');
            $track = in_array($studentRecord->academic_cycle_section_id, $trackIds, true) ? $studentRecord->academic_cycle_section_id : $trackIds[0];

            return [$syllabus->id => [
                'summary' => collect($this->coverage->summary($syllabus))->firstWhere('id', $track),
                'coverages' => $this->coverage->coverageFor($syllabus, $track),
                'currentWeek' => $syllabus->teachingWeekOn(),
            ]];
        });

        return view('pages.portal.syllabi', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'syllabi' => $syllabi,
            'progress' => $progress,
        ]);
    }
}
