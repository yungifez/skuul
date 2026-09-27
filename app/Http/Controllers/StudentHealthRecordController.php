<?php

namespace App\Http\Controllers;

use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use Illuminate\Contracts\View\View;

/**
 * Keep the health facts the school needs in an emergency.
 *
 * One child has one record. Reading a student profile does not open this, so
 * the screens live apart from the student screens.
 */
class StudentHealthRecordController extends Controller
{
    /**
     * Show every learner, and whether the school holds their health facts.
     */
    public function index(): View
    {
        $this->authorize('viewAny', StudentHealthRecord::class);

        return view('pages.health-record.index');
    }

    /**
     * Show the health record of one learner.
     */
    public function edit(StudentRecord $studentRecord): View
    {
        $this->authorize('viewAny', StudentHealthRecord::class);

        abort_unless($studentRecord->school_id === current_school_id(), 404);

        $studentRecord->load('user:id,name');

        return view('pages.health-record.edit', ['enrollment' => $studentRecord]);
    }
}
