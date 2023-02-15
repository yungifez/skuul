<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Admin\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', [User::class, 'admin']);
        $this->admin->createAdmin($request);

        return back()->with('success', 'Admin Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $admin): View
    {
        $this->authorize('view', [$admin, 'admin']);

        return view('pages.admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $admin): View
    {
        $this->authorize('update', [$admin, 'admin']);

        return view('pages.admin.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $admin): RedirectResponse
    {
        $this->authorize('update', [$admin, 'admin']);
        $this->admin->updateAdmin($admin, $request->except('_token', '_method'));

        return back()->with('success', 'Admin Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $admin): RedirectResponse
    {
        $this->authorize('delete', [$admin, 'admin']);
        $this->admin->deleteAdmin($admin);

        return back()->with('success', 'Admin Deleted Successfully');
    }
}
