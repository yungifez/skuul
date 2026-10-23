<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Models\Timetable;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use App\Services\Timetable\PersonalWeek;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

/**
 * Show a learner and their guardian the learner's week.
 *
 * The week puts together every published timetable that applies to the
 * learner, and only the lessons they take.
 */
class PortalTimetableController extends Controller
{
    public function __construct(
        private PortalAccess $access,
        private PortalSummary $summary,
        private PersonalWeek $week,
    ) {}

    /**
     * Read the learner's week from the timetables in use.
     */
    public function show(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Timetable, $studentRecord->school_id), 404);

        $studentRecord->load('user', 'school:id,name');
        $timetables = $this->summary->timetables($studentRecord);

        return view('pages.portal.timetable', [
            'studentRecord' => $studentRecord,
            'timetables' => $timetables,
            'days' => $timetables->isEmpty() ? [] : $this->weekFor($timetables, $studentRecord),
        ]);
    }

    /**
     * Build the week as the learner sees it at their own campus.
     *
     * Roles belong to a campus. A guardian may be working at another one, so
     * the learner's roles are read at the learner's campus.
     *
     * @param  Collection<int, Timetable>  $timetables
     * @return array<int, array{weekday: string, lessons: list<array{start: string, stop: string, name: string, kind: string, teachers: array<int, string>, timetable: string}>}>
     */
    private function weekFor(Collection $timetables, StudentRecord $studentRecord): array
    {
        $registrar = app(PermissionRegistrar::class);
        $before = $registrar->getPermissionsTeamId();
        $learner = $studentRecord->user;

        try {
            $registrar->setPermissionsTeamId($studentRecord->school_id);
            $learner?->unsetRelation('roles')->unsetRelation('permissions');

            return $this->week->of($timetables, $learner);
        } finally {
            $registrar->setPermissionsTeamId($before);
            $learner?->unsetRelation('roles')->unsetRelation('permissions');
        }
    }
}
