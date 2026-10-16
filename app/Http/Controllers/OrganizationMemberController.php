<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationMemberController extends Controller
{
    /**
     * Show who administers the organization, and how much of it.
     */
    public function index(Organization $organization): View
    {
        Gate::authorize('manageMembers', $organization);

        return view('pages.organization.members', compact('organization'));
    }
}
