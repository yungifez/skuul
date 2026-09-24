<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Enums\ProgramType;
use App\Models\ProgramParticipation;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show a learner and their guardian the activities the learner takes part in.
 */
class PortalProgramController extends Controller
{
    public function __construct(private PortalAccess $access) {}

    /**
     * Read extracurricular clubs and activities for one enrollment.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Programmes, $studentRecord->school_id), 404);

        $participations = ProgramParticipation::query()
            ->inSchool($studentRecord->school_id)
            ->where('student_record_id', $studentRecord->id)
            ->whereHas('program', fn (Builder $query) => $query
                ->inSchool($studentRecord->school_id)
                ->whereIn('type', [ProgramType::Club->value, ProgramType::Extracurricular->value]))
            ->with('program:id,name,type,description')
            ->orderByDesc('id')
            ->get();

        return view('pages.portal.programmes', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'participations' => $participations,
        ]);
    }
}
