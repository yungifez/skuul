<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Models\Timetable;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use App\Services\Timetable\TimetableGrid;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

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
            'grid' => $timetable === null ? null : $this->gridFor($timetable, $studentRecord),
        ]);
    }

    /**
     * Build the week as the learner sees it at their own campus.
     *
     * Roles belong to a campus. A guardian may be working at another one, so
     * the learner's roles are read at the learner's campus.
     *
     * @return array<string, mixed>
     */
    private function gridFor(Timetable $timetable, StudentRecord $studentRecord): array
    {
        $registrar = app(PermissionRegistrar::class);
        $before = $registrar->getPermissionsTeamId();
        $learner = $studentRecord->user;

        try {
            $registrar->setPermissionsTeamId($studentRecord->school_id);
            $learner?->unsetRelation('roles')->unsetRelation('permissions');

            return $this->grid->of($timetable, viewer: $learner);
        } finally {
            $registrar->setPermissionsTeamId($before);
            $learner?->unsetRelation('roles')->unsetRelation('permissions');
        }
    }
}
