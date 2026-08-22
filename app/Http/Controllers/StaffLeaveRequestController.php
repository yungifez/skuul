<?php

namespace App\Http\Controllers;

use App\Actions\Staff\ManageStaffLeave;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreStaffLeaveRequest;
use App\Http\Requests\UpdateStaffLeaveStatusRequest;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Ask for days away, and answer the request.
 *
 * A person never answers their own request. The policy holds that rule; this
 * controller only carries the answer to the action.
 */
class StaffLeaveRequestController extends Controller
{
    public function __construct(private ManageStaffLeave $manageStaffLeave) {}

    /**
     * Show the leave the school has been asked for.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StaffLeaveRequest::class);

        $selectedStatus = LeaveStatus::tryFrom($request->string('status')->toString());
        $selectedType = LeaveType::tryFrom($request->string('type')->toString());

        $leaveRequests = StaffLeaveRequest::query()
            ->inSchool()
            ->with(['staffProfile.user:id,name', 'decidedBy:id,name'])
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->orderByDesc('starts_on')
            ->paginate(20)
            ->withQueryString();

        return view('pages.staff-leave.index', [
            'leaveRequests' => $leaveRequests,
            'statuses' => LeaveStatus::cases(),
            'types' => LeaveType::cases(),
            'selectedStatus' => $selectedStatus,
            'selectedType' => $selectedType,
            'profiles' => StaffProfile::query()->inSchool()->employed()->with('user:id,name')->orderBy('id')->get(),
            'waitingCount' => StaffLeaveRequest::query()->inSchool()->where('status', LeaveStatus::Requested)->count(),
            'awayToday' => StaffProfile::query()->inSchool()->awayOn(now())->with('user:id,name')->get(),
        ]);
    }

    /**
     * Ask for days away.
     */
    public function store(StoreStaffLeaveRequest $request): RedirectResponse
    {
        $profile = StaffProfile::query()->inSchool()->findOrFail($request->integer('staff_profile_id'));

        try {
            $this->manageStaffLeave->request(
                profile: $profile,
                startsOn: $request->string('starts_on')->toString(),
                endsOn: $request->string('ends_on')->toString(),
                type: LeaveType::from($request->string('type')->toString()),
                reason: $request->string('reason')->toString() ?: null,
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['leave' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', 'The leave was asked for.');
    }

    /**
     * Answer the request, or withdraw it.
     */
    public function changeStatus(UpdateStaffLeaveStatusRequest $request, StaffLeaveRequest $staffLeaveRequest): RedirectResponse
    {
        try {
            $this->manageStaffLeave->changeStatus(
                request: $staffLeaveRequest,
                status: LeaveStatus::from($request->string('status')->toString()),
                actor: $request->user(),
                reason: $request->string('reason')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return back()->with('success', 'The leave is now '.$staffLeaveRequest->fresh()->status->label().'.');
    }
}
