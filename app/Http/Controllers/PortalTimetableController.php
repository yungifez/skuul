<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use App\Services\Timetable\TimetableGrid;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show a learner and their guardian the week of the learner's class.
 *
 * Only a published timetable is shown, and only the lessons the learner takes.
 */
class PortalTimetableController extends Controller
{
    public function __construct(
        private PortalAccess $access,
        private PortalSummary $summary,
        private TimetableGrid $grid,
    ) {}

    /**
     * Read the newest published timetable of the learner's class.
     */
    public function show(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Timetable, $studentRecord->school_id), 404);

        $studentRecord->load('user', 'school:id,name');
        $timetable = $this->summary->timetable($studentRecord);

        return view('pages.portal.timetable', [
            'studentRecord' => $studentRecord,
            'timetable' => $timetable,
            'grid' => $timetable === null ? null : $this->grid->of($timetable, viewer: $studentRecord->user),
        ]);
    }
}
