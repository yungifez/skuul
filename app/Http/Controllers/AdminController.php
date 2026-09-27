<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Admin\AdminService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public $admin;

    public function __construct(AdminService $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', [User::class, 'admin']);

        return view('pages.admin.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', [User::class, 'admin']);

        return view('pages.admin.create');
    }

    /**
     * Display the specified resource.
     *
     * @throws AuthorizationException
     */
    public function show(User $admin): View
    {
        $this->authorize('view', [$admin, 'admin']);

        return view('pages.admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function edit(User $admin): View
    {
        $this->authorize('update', [$admin, 'admin']);

        return view('pages.admin.edit', compact('admin'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws AuthorizationException
     */
    public function destroy(User $admin): RedirectResponse
    {
        $this->authorize('delete', [$admin, 'admin']);
        $this->admin->deleteAdmin($admin);

        return back()->with('success', 'Admin Deleted Successfully');
    }
}
