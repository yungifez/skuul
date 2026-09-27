<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The web addresses an organization answers on.
 */
class OrganizationDomainController extends Controller
{
    /**
     * Show the addresses, and how to prove a new one.
     */
    public function index(Organization $organization): View
    {
        Gate::authorize('manageDomains', $organization);

        return view('pages.organization.domains', ['organization' => $organization]);
    }
}
