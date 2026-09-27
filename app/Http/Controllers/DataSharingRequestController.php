<?php

namespace App\Http\Controllers;

use App\Actions\Sharing\RequestDataSharing;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreDataSharingRequestRequest;
use App\Models\DataSharingRequest;
use App\Models\School;
use App\Models\StudentRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Ask another school for a learner's records, and answer such a request.
 *
 * Asking, approving, and handing over are three separate decisions, and the
 * receiving school still has to take the package in. Nothing crosses a school
 * boundary because one person clicked once.
 */
class DataSharingRequestController extends Controller
{
    public function __construct(
        private RequestDataSharing $requestSharing,
    ) {}

    /**
     * Show what this school asked for, and what it was asked for.
     */
    public function index(): View
    {
        $this->authorize('viewAny', DataSharingRequest::class);

        $school = current_school_id();

        $asked = DataSharingRequest::query()
            ->where('requesting_school_id', $school)
            ->with(['holdingSchool:id,name', 'studentRecord:id,admission_number'])
            ->latest('id')
            ->get();

        $received = DataSharingRequest::query()
            ->where('holding_school_id', $school)
            ->with(['requestingSchool:id,name', 'studentRecord.user:id,name'])
            ->latest('id')
            ->get();

        return view('pages.data-sharing.index', [
            'asked' => $asked,
            'received' => $received,
            'waitingCount' => $received->where('status', DataSharingStatus::Requested)->count(),
        ]);
    }

    /**
     * Show the form that asks another school.
     */
    public function create(): View
    {
        $this->authorize('create', DataSharingRequest::class);

        return view('pages.data-sharing.create', [
            'schools' => School::query()
                ->whereKeyNot(current_school_id())
                ->orderBy('name')
                ->get(['id', 'name']),
            'categories' => DataCategory::cases(),
        ]);
    }

    /**
     * Ask the school that holds the records.
     */
    public function store(StoreDataSharingRequestRequest $request): RedirectResponse
    {
        $enrollment = StudentRecord::query()
            ->where('school_id', $request->integer('holding_school_id'))
            ->where('admission_number', $request->string('admission_number')->toString())
            ->first();

        if ($enrollment === null) {
            // The message says nothing about which half was wrong, so a wrong
            // guess never tells one school who attends another.
            return back()
                ->withErrors(['admission_number' => 'That school holds no learner with that admission number.'])
                ->withInput();
        }

        try {
            $sharingRequest = $this->requestSharing->request(
                enrollment: $enrollment,
                requestingSchool: current_school(),
                purpose: $request->string('purpose')->toString(),
                categories: array_map(
                    fn (string $value): DataCategory => DataCategory::from($value),
                    $request->input('categories', []),
                ),
                expiresOn: $request->string('expires_on')->toString() ?: null,
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['data_sharing' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('data-sharing-requests.show', $sharingRequest)
            ->with('success', 'The request was sent to the school that holds the records.');
    }

    /**
     * Show one request, and the package it produced.
     */
    public function show(DataSharingRequest $dataSharingRequest): View
    {
        $this->authorize('view', $dataSharingRequest);

        return view('pages.data-sharing.show', ['sharingRequest' => $dataSharingRequest]);
    }
}
