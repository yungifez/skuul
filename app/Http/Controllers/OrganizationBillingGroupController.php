<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Which campuses of an organization keep one purse.
 *
 * A district whose campuses share a finance office bills a family once. A
 * district whose campuses keep their own accounts does not. Neither is the
 * right answer everywhere, so the organization says which it is.
 */
class OrganizationBillingGroupController extends Controller
{
    /**
     * Show the groups and which campus is in which.
     */
    public function index(Organization $organization): View
    {
        Gate::authorize('manageDomains', $organization);

        return view('pages.organization.billing-groups', ['organization' => $organization]);
    }
}
