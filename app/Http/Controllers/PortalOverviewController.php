<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Read authorized family records across campuses.
 * Each record retains its source campus; changes use campus-specific screens.
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
     * @return array<int, list<PortalArea>>
     */
    private function openAreas(Collection $enrollments): array
    {
        return $enrollments
            ->mapWithKeys(fn (StudentRecord $enrollment): array => [$enrollment->id => $this->access->openAreas($enrollment)])
            ->all();
    }
}
