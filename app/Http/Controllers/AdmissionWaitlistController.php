<?php

namespace App\Http\Controllers;

use App\Actions\Admissions\AcceptWaitlistEntry;
use App\Actions\Admissions\DeclineWaitlistEntry;
use App\Actions\Admissions\JoinWaitlist;
use App\Actions\Admissions\OfferNextWaitlistEntry;
use App\Http\Requests\StoreAdmissionWaitlistRequest;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdmissionWaitlistController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AdmissionWaitlistEntry::class);

        return view('pages.admissions.waitlist', [
            'entries' => AdmissionWaitlistEntry::inSchool()
                ->with(['academicCycleSection.academicLevel', 'academicYear', 'candidate'])
                ->orderByDesc('priority')
                ->orderBy('position')
                ->get(),
            'sections' => AcademicCycleSection::inSchool()
                ->with(['academicLevel', 'academicYear'])
                ->where('status', 'active')
                ->whereNotNull('capacity')
                ->orderBy('academic_year_id')
                ->orderBy('academic_level_id')
                ->orderBy('position')
                ->get(),
            'candidates' => User::ofSchool()
                ->whereDoesntHave('studentRecords', function ($query): void {
                    $query->where('school_id', current_school_id())
                        ->where('status', 'active');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function store(StoreAdmissionWaitlistRequest $request, JoinWaitlist $join): RedirectResponse
    {
        $section = AcademicCycleSection::inSchool()->findOrFail($request->integer('academic_cycle_section_id'));
        $candidate = User::ofSchool()->findOrFail($request->integer('user_id'));

        $this->authorize('create', AdmissionWaitlistEntry::class);

        $join->join($section, $candidate, $request->user(), $request->integer('priority', 0));

        return back()->with('success', 'The candidate is on the admission waitlist.');
    }

    public function offer(AdmissionWaitlistEntry $admissionWaitlistEntry, OfferNextWaitlistEntry $offer): RedirectResponse
    {
        $this->authorize('update', $admissionWaitlistEntry);

        $entry = $offer->offer($admissionWaitlistEntry->academicCycleSection, request()->user());

        return back()->with($entry === null ? 'info' : 'success', $entry === null
            ? 'There is no open place or pending candidate for this section.'
            : 'The next candidate has been offered a place.');
    }

    public function accept(AdmissionWaitlistEntry $admissionWaitlistEntry, AcceptWaitlistEntry $accept): RedirectResponse
    {
        $this->authorize('update', $admissionWaitlistEntry);

        $accept->accept($admissionWaitlistEntry, request()->user());

        return back()->with('success', 'The candidate accepted the place and is now enrolled.');
    }

    public function decline(AdmissionWaitlistEntry $admissionWaitlistEntry, DeclineWaitlistEntry $decline): RedirectResponse
    {
        $this->authorize('update', $admissionWaitlistEntry);

        $decline->decline($admissionWaitlistEntry, request()->user(), request('reason'));

        return back()->with('success', 'The admission waitlist entry was declined.');
    }
}
