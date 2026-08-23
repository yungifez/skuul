<?php

namespace App\Http\Controllers;

use App\Actions\Facility\BookFacility;
use App\Enums\FacilityKind;
use App\Http\Requests\StoreFacilityBookingRequest;
use App\Http\Requests\StoreFacilityRequest;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The halls, laboratories, vehicles, and kit a campus shares.
 */
class FacilityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Facility::class, 'facility');
    }

    /**
     * Show what the campus shares and what is booked next.
     */
    public function index(): View
    {
        return view('pages.facility.index', [
            'facilities' => Facility::inSchool()->orderBy('name')->withCount([
                'bookings as upcoming_bookings_count' => fn ($query) => $query
                    ->whereNull('cancelled_at')
                    ->where('ends_at', '>=', now()),
            ])->get(),
            'bookings' => FacilityBooking::inSchool()
                ->running()
                ->where('ends_at', '>=', now())
                ->with(['facility', 'bookedBy'])
                ->orderBy('starts_at')
                ->limit(50)
                ->get(),
            'kinds' => FacilityKind::cases(),
            'canManage' => auth()->user()?->can('manage facility') === true,
            'canBook' => auth()->user()?->can('book facility') === true,
        ]);
    }

    /**
     * Add something to the catalogue.
     */
    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        Facility::create($request->validated() + ['school_id' => current_school_id()]);

        return back()->with('success', 'The campus can book it now.');
    }

    /**
     * Change what the catalogue says about it.
     */
    public function update(StoreFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $facility->update($request->validated());

        return back()->with('success', 'Saved.');
    }

    /**
     * Take it out of use.
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        // Bookings already made are the campus's history, so the record stays
        // and simply stops being something anybody can claim.
        $facility->is_active = false;
        $facility->save();

        return back()->with('success', "$facility->name is out of use.");
    }

    /**
     * Claim something for a stretch of time.
     */
    public function book(StoreFacilityBookingRequest $request, BookFacility $bookFacility): RedirectResponse
    {
        $facility = Facility::inSchool()->findOrFail($request->validated('facility_id'));

        $this->authorize('book', $facility);

        $bookFacility->book(
            facility: $facility,
            from: now()->parse($request->validated('starts_at')),
            to: now()->parse($request->validated('ends_at')),
            purpose: $request->validated('purpose'),
        );

        return back()->with('success', "$facility->name is booked.");
    }

    /**
     * Give a booking up again.
     */
    public function cancelBooking(FacilityBooking $facilityBooking, Request $request, BookFacility $bookFacility): RedirectResponse
    {
        abort_unless($facilityBooking->school_id === current_school_id(), 403);

        $this->authorize('book', $facilityBooking->facility);

        $bookFacility->cancel($facilityBooking, (string) $request->string('reason'));

        return back()->with('success', 'The booking was given up.');
    }
}
