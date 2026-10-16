<?php

namespace App\Http\Controllers;

use App\Enums\PlatformPermission;
use App\Models\Organization;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Organization::class, 'organization');
    }

    public function index(): View
    {
        $organizations = auth()->user()->can(PlatformPermission::AccessAllOrganizations)
            ? Organization::query()->withCount('schools')->orderBy('name')->get()
            : auth()->user()->organizations()->withCount('schools')->orderBy('name')->get();

        return view('pages.organization.index', compact('organizations'));
    }

    public function create(): View
    {
        return view('pages.organization.create');
    }

    public function show(Organization $organization): View
    {
        $organization->load(['schools' => fn ($query) => $query->orderBy('name')]);

        return view('pages.organization.show', compact('organization'));
    }

    public function edit(Organization $organization): View
    {
        return view('pages.organization.edit', compact('organization'));
    }
}
