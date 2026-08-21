<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationDashboardController extends Controller
{
    /**
     * Display the aggregate-only dashboard for one organization.
     */
    public function __invoke(Organization $organization): View
    {
        Gate::authorize('viewReports', $organization);

        return view('pages.organization.dashboard', compact('organization'));
    }
}
