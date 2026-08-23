<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingGroupRequest;
use App\Http\Requests\UpdateCampusBillingRequest;
use App\Models\BillingGroup;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
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

        return view('pages.organization.billing-groups', [
            'organization' => $organization,
            'groups' => $organization->billingGroups()->with('schools')->orderBy('name')->get(),
            'campuses' => $organization->schools()->with('billingGroup')->orderBy('name')->get(),
        ]);
    }

    /**
     * Start a group.
     */
    public function store(StoreBillingGroupRequest $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('manageDomains', $organization);

        $group = BillingGroup::create([
            'organization_id' => $organization->id,
            'name' => $request->string('name')->toString(),
        ]);

        return back()->with('success', "$group->name can now hold campuses.");
    }

    /**
     * Put one campus in a group, or take it out again.
     *
     * Nothing already in the books moves. A balance follows a learner only
     * when they themselves move to another campus of the same group.
     */
    public function update(UpdateCampusBillingRequest $request, Organization $organization, School $school): RedirectResponse
    {
        Gate::authorize('manageDomains', $organization);
        abort_unless($school->organization_id === $organization->id, 404);

        $groupId = $request->integer('billing_group_id');
        $school->billing_group_id = $groupId === 0 ? null : $groupId;
        $school->save();

        return back()->with('success', $school->billing_group_id === null
            ? "$school->name now bills on its own."
            : "$school->name now bills with the rest of its group.");
    }
}
