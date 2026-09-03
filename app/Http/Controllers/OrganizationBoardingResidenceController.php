<?php

namespace App\Http\Controllers;

use App\Actions\Boarding\AttachDormitoryToBoardingResidence;
use App\Actions\Boarding\CreateBoardingResidence;
use App\Actions\Boarding\LinkSchoolToBoardingResidence;
use App\Http\Requests\StoreBoardingResidenceHouseRequest;
use App\Http\Requests\StoreBoardingResidenceRequest;
use App\Http\Requests\StoreBoardingResidenceSchoolRequest;
use App\Models\BoardingResidence;
use App\Models\Dormitory;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationBoardingResidenceController extends Controller
{
    public function __construct(
        private CreateBoardingResidence $createResidence,
        private LinkSchoolToBoardingResidence $linkSchool,
        private AttachDormitoryToBoardingResidence $attachHouse,
    ) {}

    /**
     * Show shared residences and their school-owned houses.
     */
    public function index(Organization $organization): View
    {
        Gate::authorize('manageCampuses', $organization);

        return view('pages.organization.boarding-residences', [
            'organization' => $organization,
            'campuses' => $organization->schools()->orderBy('name')->get(),
            'residences' => $organization->boardingResidences()
                ->with([
                    'schools' => fn ($query) => $query->orderBy('name'),
                    'dormitories' => fn ($query) => $query->with('school')->orderBy('name'),
                ])
                ->orderBy('name')
                ->get(),
            'availableHouses' => Dormitory::query()
                ->whereNull('boarding_residence_id')
                ->whereHas('school', fn ($query) => $query->where('organization_id', $organization->id))
                ->with('school')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Create a new shared physical residence.
     */
    public function store(StoreBoardingResidenceRequest $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('manageCampuses', $organization);

        $residence = $this->createResidence->create(
            organization: $organization,
            name: $request->string('name')->toString(),
            notes: $request->string('notes')->toString() ?: null,
            actor: $request->user(),
        );

        return back()->with('success', "$residence->name is ready to share between campuses.");
    }

    /**
     * Link a campus to a shared residence.
     */
    public function linkSchool(StoreBoardingResidenceSchoolRequest $request, Organization $organization, BoardingResidence $boardingResidence): RedirectResponse
    {
        $this->authorizeResidence($organization, $boardingResidence);

        $school = School::findOrFail($request->integer('school_id'));
        abort_unless($school->organization_id === $organization->id, 404);

        $this->linkSchool->link($boardingResidence, $school, request()->user());

        return back()->with('success', "$school->name can now use $boardingResidence->name.");
    }

    /**
     * Remove a campus from a residence.
     */
    public function unlinkSchool(Organization $organization, BoardingResidence $boardingResidence, School $school): RedirectResponse
    {
        $this->authorizeResidence($organization, $boardingResidence);
        abort_unless($school->organization_id === $organization->id, 404);

        $this->linkSchool->unlink($boardingResidence, $school, request()->user());

        return back()->with('success', "$school->name no longer uses $boardingResidence->name.");
    }

    /**
     * Put a school-owned house in a shared residence.
     */
    public function attachHouse(StoreBoardingResidenceHouseRequest $request, Organization $organization, BoardingResidence $boardingResidence): RedirectResponse
    {
        $this->authorizeResidence($organization, $boardingResidence);

        $dormitory = Dormitory::findOrFail($request->integer('dormitory_id'));
        abort_unless($dormitory->school()->where('organization_id', $organization->id)->exists(), 404);

        $this->attachHouse->attach($boardingResidence, $dormitory, request()->user());

        return back()->with('success', "$dormitory->name is now in $boardingResidence->name.");
    }

    /**
     * Take a house out of a shared residence without deleting the house.
     */
    public function detachHouse(Organization $organization, BoardingResidence $boardingResidence, Dormitory $dormitory): RedirectResponse
    {
        $this->authorizeResidence($organization, $boardingResidence);
        abort_unless($dormitory->school()->where('organization_id', $organization->id)->exists(), 404);

        $this->attachHouse->detach($boardingResidence, $dormitory, request()->user());

        return back()->with('success', "$dormitory->name is no longer in $boardingResidence->name.");
    }

    private function authorizeResidence(Organization $organization, BoardingResidence $residence): void
    {
        Gate::authorize('manageCampuses', $organization);
        abort_unless($residence->organization_id === $organization->id, 404);
    }
}
