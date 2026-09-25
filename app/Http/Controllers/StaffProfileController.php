<?php

namespace App\Http\Controllers;

use App\Enums\EmploymentType;
use App\Enums\StaffStatus;
use App\Http\Requests\StoreStaffAvailabilityRequest;
use App\Http\Requests\StoreStaffCredentialRequest;
use App\Http\Requests\StoreStaffProfileRequest;
use App\Http\Requests\UpdateStaffProfileRequest;
use App\Models\StaffAvailability;
use App\Models\StaffCredential;
use App\Models\StaffProfile;
use App\Services\Staff\StaffAvailability as StaffAvailabilityService;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Who works here, in what job, and when they can take work.
 */
class StaffProfileController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private StaffAvailabilityService $availability) {}

    /**
     * Show the people who work in this school.
     */
    public function index(): View
    {
        $this->authorize('viewAny', StaffProfile::class);

        return view('pages.staff-profile.index');
    }

    /**
     * Show the form that writes an employment record.
     */
    public function create(): View
    {
        $this->authorize('create', StaffProfile::class);

        return view('pages.staff-profile.create', [
            'employmentTypes' => EmploymentType::cases(),
            'people' => $this->schoolStaff(),
        ]);
    }

    /**
     * Write an employment record.
     */
    public function store(StoreStaffProfileRequest $request): RedirectResponse
    {
        $profile = StaffProfile::create([
            'school_id' => current_school_id(),
            ...$request->validated(),
        ]);

        return redirect()->route('staff-profiles.show', $profile)->with('success', 'The employment record was saved.');
    }

    /**
     * Show one person's employment record.
     */
    public function show(StaffProfile $staffProfile): View
    {
        $this->authorize('view', $staffProfile);

        $staffProfile->load([
            'user:id,name,email',
            'credentials',
            'availabilities',
            'leaveRequests.statusChanges',
        ]);

        return view('pages.staff-profile.show', [
            'profile' => $staffProfile,
            'employmentTypes' => EmploymentType::cases(),
            'statuses' => StaffStatus::cases(),
            'isAway' => $this->availability->isAway($staffProfile, now()),
        ]);
    }

    /**
     * Change the job, the hours, or the state of an employment record.
     */
    public function update(UpdateStaffProfileRequest $request, StaffProfile $staffProfile): RedirectResponse
    {
        $staffProfile->update($request->validated());

        return back()->with('success', 'The employment record was saved.');
    }

    /**
     * Record what the person is qualified for.
     */
    public function storeCredential(StoreStaffCredentialRequest $request, StaffProfile $staffProfile): RedirectResponse
    {
        StaffCredential::create([
            'staff_profile_id' => $staffProfile->id,
            ...$request->validated(),
        ]);

        return back()->with('success', 'The qualification was added.');
    }

    /**
     * Record the hours the person can work.
     */
    public function storeAvailability(StoreStaffAvailabilityRequest $request, StaffProfile $staffProfile): RedirectResponse
    {
        StaffAvailability::create([
            'staff_profile_id' => $staffProfile->id,
            ...$request->validated(),
        ]);

        return back()->with('success', 'The working hours were added.');
    }
}
