<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationBoardingResidenceController extends Controller
{
    /**
     * Show shared residences and their school-owned houses.
     */
    public function index(Organization $organization): View
    {
        Gate::authorize('manageCampuses', $organization);

        return view('pages.organization.boarding-residences', ['organization' => $organization]);
    }
}
