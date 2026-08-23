<?php

namespace App\Http\Controllers;

use App\Actions\Boarding\DecideOvernightLeave;
use App\Actions\Boarding\RequestOvernightLeave;
use App\Enums\OvernightLeaveStatus;
use App\Http\Requests\StoreOvernightLeaveRequest;
use App\Models\OvernightLeave;
use App\Models\StudentRecord;
use App\Traits\ListsSchoolPeople;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Nights a boarder spends away from the house.
 */
class OvernightLeaveController extends Controller
{
    use ListsSchoolPeople;

    /**
     * Show the requests waiting, and who is out tonight.
     */
    public function index(): View
    {
        $this->authorize('viewAny', OvernightLeave::class);

        return view('pages.boarding.leave.index', [
            'waiting' => OvernightLeave::inSchool()->waiting()->with('studentRecord.user')->orderBy('leaves_on')->get(),
            'tonight' => OvernightLeave::inSchool()->awayOn()->with('studentRecord.user')->orderBy('returns_on')->get(),
            'recent' => OvernightLeave::inSchool()->whereNot('status', OvernightLeaveStatus::Requested)
                ->with(['studentRecord.user', 'decidedBy'])
                ->orderByDesc('id')
                ->limit(25)
                ->get(),
            'learners' => $this->schoolLearners(),
            'canDecide' => auth()->user()?->can('decide overnight leave') === true,
            'canAsk' => auth()->user()?->can('manage boarding') === true,
        ]);
    }

    /**
     * Ask for a night away.
     */
    public function store(StoreOvernightLeaveRequest $request, RequestOvernightLeave $ask): RedirectResponse
    {
        $this->authorize('create', OvernightLeave::class);

        $enrollment = StudentRecord::inSchool()->findOrFail($request->validated('student_record_id'));

        $ask->request(
            enrollment: $enrollment,
            leavesOn: $request->validated('leaves_on'),
            returnsOn: $request->validated('returns_on'),
            destination: $request->validated('destination'),
            contact: $request->validated('contact'),
            reason: $request->validated('reason'),
        );

        return back()->with('success', 'The request is waiting for a decision.');
    }

    /**
     * Answer a request, or record the learner coming back.
     */
    public function update(OvernightLeave $overnightLeave, Request $request, DecideOvernightLeave $decide): RedirectResponse
    {
        $this->authorize('decide', $overnightLeave);

        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', OvernightLeaveStatus::values()),
            'note' => 'nullable|string|max:1000',
        ]);

        $status = OvernightLeaveStatus::from($validated['status']);

        $decide->decide($overnightLeave, $status, $validated['note'] ?? null);

        return back()->with('success', 'The request was answered.');
    }
}
