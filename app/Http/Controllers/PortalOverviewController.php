<?php

namespace App\Http\Controllers;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Everything one family may read, across every campus at once.
 *
 * A guardian with children at two campuses, or a learner enrolled at two,
 * should not have to know which campus a record lives in before they can find
 * it. This screen reads only: every campus record names its campus, and
 * anything that changes a record happens on that campus's own screens.
 */
class PortalOverviewController extends Controller
{
    public function __construct(private PortalAccess $access) {}

    /**
     * Show every enrollment this person may read.
     */
    public function index(Request $request): View
    {
        $enrollments = $this->access->enrollmentsFor($request->user())
            ->load('school', 'user');

        abort_if($enrollments->isEmpty(), 404);

        return view('pages.portal.overview', [
            'campuses' => $this->groupByCampus($enrollments),
            'areasOf' => $this->openAreas($enrollments),
        ]);
    }

    /**
     * Group the enrollments by the campus they belong to.
     *
     * @param  Collection<int, StudentRecord>  $enrollments
     * @return Collection<int, Collection<int, StudentRecord>>
     */
    private function groupByCampus(Collection $enrollments): Collection
    {
        return $enrollments->groupBy(fn (StudentRecord $enrollment): string => $enrollment->school->name);
    }

    /**
     * Work out which areas each enrollment can be opened in.
     *
     * A campus that closed the portal, or one part of it, offers no link at
     * all rather than a link that refuses.
     *
     * @param  Collection<int, StudentRecord>  $enrollments
     * @return array<int, array<int, PortalArea>>
     */
    private function openAreas(Collection $enrollments): array
    {
        $areas = [];

        foreach ($enrollments as $enrollment) {
            $open = [];

            foreach ([PortalArea::Attendance, PortalArea::Notices, PortalArea::Calendar, PortalArea::Documents, PortalArea::Boarding, PortalArea::Library, PortalArea::Requests] as $area) {
                if ($area === PortalArea::Boarding && !features()->enabled(Feature::Boarding, $enrollment->school_id)) {
                    continue;
                }

                if ($this->access->areaIsOpen($area, $enrollment->school_id)) {
                    $open[] = $area;
                }
            }

            $areas[$enrollment->id] = $open;
        }

        return $areas;
    }
}
