<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use Illuminate\Contracts\View\View;

/**
 * Who works here, in what job, and when they can take work.
 */
class StaffProfileController extends Controller
{
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

        return view('pages.staff-profile.create');
    }

    /**
     * Show one person's employment record.
     */
    public function show(StaffProfile $staffProfile): View
    {
        $this->authorize('view', $staffProfile);

        $staffProfile->load('user:id,name');

        return view('pages.staff-profile.show', ['profile' => $staffProfile]);
    }
}
