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

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $organizations = auth()->user()->can(PlatformPermission::AccessAllOrganizations)
            ? Organization::query()->withCount('schools')->orderBy('name')->get()
            : auth()->user()->organizations()->withCount('schools')->orderBy('name')->get();

        return view('pages.organization.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.organization.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization): View
    {
        $organization->load(['schools' => fn ($query) => $query->orderBy('name')]);

        return view('pages.organization.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): View
    {
        return view('pages.organization.edit', compact('organization'));
    }
}
