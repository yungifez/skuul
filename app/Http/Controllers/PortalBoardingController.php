<?php

namespace App\Http\Controllers;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show a boarder their current house placement.
 */
class PortalBoardingController extends Controller
{
    public function __construct(private PortalAccess $access, private PortalSummary $summary) {}

    /**
     * Show the current place, if the learner boards.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Boarding, $studentRecord->school_id), 404);
        abort_unless(features()->enabled(Feature::Boarding, $studentRecord->school_id), 404);

        $boarding = $this->summary->boarding($studentRecord);
        abort_if($boarding === null, 404);

        return view('pages.portal.boarding', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'place' => $boarding['place'],
        ]);
    }
}
