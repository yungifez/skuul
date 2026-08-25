<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalLibraryController extends Controller
{
    public function __construct(private PortalAccess $access, private PortalSummary $summary) {}

    /**
     * Show the books this learner has and the titles they are waiting for.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Library, $studentRecord->school_id), 404);

        $library = $this->summary->library($studentRecord);

        if ($library === null) {
            abort(404);
        }

        return view('pages.portal.library', [
            'studentRecord' => $studentRecord,
            'loans' => $library['loans'],
            'reservations' => $library['reservations'],
        ]);
    }
}
