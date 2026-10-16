<?php

namespace App\Http\Controllers;

use App\Enums\DataSharingStatus;
use App\Models\DataSharingRequest;
use Illuminate\Contracts\View\View;

/**
 * Keep sharing request, approval, fulfilment, and receipt as separate workflow steps.
 */
class DataSharingRequestController extends Controller
{
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

        return view('pages.data-sharing.create');
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
