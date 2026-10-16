<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show a learner and their guardian the approved result of each course.
 *
 * A revision that still waits for approval stays out of sight, so the family
 * never reads a mark the school has not agreed to.
 */
class PortalResultController extends Controller
{
    public function __construct(private PortalAccess $access, private PortalSummary $summary) {}

    /**
     * Read the latest approved result of each course of one enrollment.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Results, $studentRecord->school_id), 404);

        $results = (new EloquentCollection($this->summary->results($studentRecord)->all()))
            ->load('courseOffering.academicPeriod')
            ->sortBy([
                fn (ResultSnapshot $a, ResultSnapshot $b): int => ($b->courseOffering->academicPeriod->starts_on->timestamp ?? 0) <=> ($a->courseOffering->academicPeriod->starts_on->timestamp ?? 0),
                fn (ResultSnapshot $a, ResultSnapshot $b): int => strcmp($a->courseOffering->subject->name ?? '', $b->courseOffering->subject->name ?? ''),
            ])
            ->values();

        return view('pages.portal.results', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'results' => $results,
        ]);
    }
}
