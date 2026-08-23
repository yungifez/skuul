<?php

namespace App\Http\Controllers;

use App\Actions\Authorization\AssignCampusRole;
use App\Actions\Authorization\WriteCampusRole;
use App\Http\Requests\AssignCampusRoleRequest;
use App\Http\Requests\StoreCampusRoleRequest;
use App\Http\Requests\UpdateCampusRoleRequest;
use App\Models\CampusRole;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The roles this campus offers.
 *
 * A role is a named set of permissions, so a campus can invent Registrar or
 * Finance Officer without waiting for the application to learn those words.
 */
class CampusRoleController extends Controller
{
    public function __construct(
        private WriteCampusRole $writeRole,
        private AssignCampusRole $assignRole,
        private RoleAuthority $authority,
    ) {}

    /**
     * Show the roles of the campus being worked in.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', CampusRole::class);

        return view('pages.role.index', [
            // The campus's own roles, and the built-in ones every campus
            // shares. A role another campus wrote is not this campus's
            // business at all.
            'roles' => CampusRole::query()
                ->where(fn ($query) => $query->inSchool()->orWhereNull('school_id'))
                ->withCount(['users', 'permissions'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for writing a role.
     */
    public function create(): View
    {
        Gate::authorize('create', CampusRole::class);

        return view('pages.role.create', [
            'grantable' => $this->authority->grantableBy(auth()->user(), current_school()),
        ]);
    }

    /**
     * Write the role.
     */
    public function store(StoreCampusRoleRequest $request): RedirectResponse
    {
        Gate::authorize('create', CampusRole::class);

        $role = $this->writeRole->create(
            school: current_school(),
            name: $request->string('name')->toString(),
            permissions: $request->input('permissions', []),
            description: $request->input('description'),
            actor: $request->user(),
        );

        return redirect()
            ->route('roles.edit', $role->id)
            ->with('success', "$role->name is ready. Give it to somebody below.");
    }

    /**
     * Show the form for changing a role and the people holding it.
     */
    public function edit(CampusRole $role): View
    {
        Gate::authorize('assign', $role);

        return view('pages.role.edit', [
            'role' => $role->load('permissions'),
            'grantable' => $this->authority->grantableBy(auth()->user(), current_school()),
            'holders' => $role->users()->orderBy('name')->get(),
            'canWrite' => auth()->user()->can('update', $role),
            'members' => User::ofSchool()->orderBy('name')->get(['users.id', 'users.name', 'users.email']),
        ]);
    }

    /**
     * Change what the role holds.
     */
    public function update(UpdateCampusRoleRequest $request, CampusRole $role): RedirectResponse
    {
        Gate::authorize('update', $role);

        $this->writeRole->update(
            role: $role,
            school: current_school(),
            permissions: $request->input('permissions', []),
            description: $request->input('description'),
            actor: $request->user(),
        );

        return back()->with('success', "$role->name was changed.");
    }

    /**
     * Copy a role a campus already trusts.
     */
    public function duplicate(StoreCampusRoleRequest $request, CampusRole $role): RedirectResponse
    {
        Gate::authorize('create', CampusRole::class);
        Gate::authorize('assign', $role);

        $copy = $this->writeRole->duplicate(
            role: $role,
            school: current_school(),
            name: $request->string('name')->toString(),
            actor: $request->user(),
        );

        return redirect()->route('roles.edit', $copy->id)->with('success', "$copy->name is a copy of $role->name.");
    }

    /**
     * Stop offering the role, without taking it from its holders.
     */
    public function archive(CampusRole $role): RedirectResponse
    {
        Gate::authorize('archive', $role);

        $this->writeRole->archive($role, current_school(), request()->user());

        return back()->with('success', "$role->name is no longer offered. The people holding it keep it.");
    }

    /**
     * Offer the role again.
     */
    public function restore(CampusRole $role): RedirectResponse
    {
        Gate::authorize('archive', $role);

        $this->writeRole->restore($role, current_school(), request()->user());

        return back()->with('success', "$role->name is offered again.");
    }

    /**
     * Give the role to somebody who works at this campus.
     */
    public function give(AssignCampusRoleRequest $request, CampusRole $role): RedirectResponse
    {
        Gate::authorize('assign', $role);

        $this->assignRole->give(
            User::findOrFail($request->integer('user_id')),
            $role,
            current_school(),
            $request->user(),
        );

        return back()->with('success', "The role was given to that person at {$role->school?->name}.");
    }

    /**
     * Take the role away again.
     */
    public function take(AssignCampusRoleRequest $request, CampusRole $role): RedirectResponse
    {
        Gate::authorize('assign', $role);

        $this->assignRole->take(
            User::findOrFail($request->integer('user_id')),
            $role,
            current_school(),
            $request->user(),
        );

        return back()->with('success', 'The role was taken away.');
    }
}
