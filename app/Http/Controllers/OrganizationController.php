<?php

namespace App\Http\Controllers;

use App\Actions\Organization\CreateOrganization;
use App\Actions\Organization\UpdateOrganization;
use App\Enums\PlatformPermission;
use App\Http\Requests\OrganizationStoreRequest;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function __construct(
        private CreateOrganization $createOrganization,
        private UpdateOrganization $updateOrganization,
    ) {
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
     * Store a newly created resource in storage.
     */
    public function store(OrganizationStoreRequest $request): RedirectResponse
    {
        $organization = $this->createOrganization->create($request->validated(), $request->user());

        return redirect()->route('organizations.show', $organization)->with('success', __('Organization created successfully'));
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

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationUpdateRequest $request, Organization $organization): RedirectResponse
    {
        $this->updateOrganization->update($organization, $request->validated(), $request->user());

        return redirect()->route('organizations.show', $organization)->with('success', __('Organization updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        abort(405);
    }
}
