<?php

namespace App\Http\Controllers;

use App\Models\CampusRole;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The roles this campus offers.
 *
 * A role is a named set of permissions, so a campus can invent Registrar or
 * Finance Officer without waiting for the application to learn those words.
 * Writing and handing out roles happens in the Livewire components.
 */
class CampusRoleController extends Controller
{
    public function __construct(private RoleAuthority $authority) {}

    /**
     * Show the roles of the campus being worked in.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', CampusRole::class);

        $schoolId = current_school_id();
        $pivot = config('permission.table_names.model_has_roles');
        $campusColumn = config('permission.column_names.team_foreign_key');

        $roles = CampusRole::query()
            // The campus's own roles, and shared roles available to every
            // campus. A role another campus wrote is not this campus's
            // business at all, and neither are its holders there.
            ->where(fn ($query) => $query->inSchool()->orWhereNull('school_id'))
            ->withCount([
                'users' => fn ($query) => $query->where("$pivot.$campusColumn", $schoolId),
                'permissions',
            ])
            ->orderBy('name')
            ->get();

        // A shared role the campus keeps its own copy of is shown once, as the copy.
        $ownNames = $roles->whereNotNull('school_id')->map(fn (CampusRole $role): string => mb_strtolower($role->name))->all();

        return view('pages.role.index', [
            'roles' => $roles
                ->reject(fn (CampusRole $role): bool => $role->school_id === null && in_array(mb_strtolower($role->name), $ownNames, true))
                ->values(),
        ]);
    }

    /**
     * Show the form for writing a role.
     */
    public function create(): View
    {
        Gate::authorize('create', CampusRole::class);

        return view('pages.role.create');
    }

    /**
     * Show a role and the people holding it at this campus.
     */
    public function edit(CampusRole $role): View|RedirectResponse
    {
        Gate::authorize('assign', $role);

        $copy = $this->authority->tailoredCopyOf($role, current_school());

        if ($copy !== null) {
            return redirect()->route('roles.edit', $copy->id);
        }

        return view('pages.role.edit', ['role' => $role]);
    }
}
