<?php

namespace App\Http\Controllers;

use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalAttendanceController extends Controller
{
    public function __construct(private PortalAccess $access, private PortalSummary $summary)
    {
    }

    public function show(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        $attendance = $this->summary->attendance($studentRecord);
        abort_if($attendance === null, 404);

        return view('pages.portal.attendance', compact('attendance', 'studentRecord'));
    }
}
