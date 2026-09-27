<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Parent\ParentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParentController extends Controller
{
    /**
     * ParentService variable.
     */
    public ParentService $parentService;

    public function __construct(ParentService $parentService)
    {
        $this->parentService = $parentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', [User::class, 'parent']);

        return view('pages.parent.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', [User::class, 'parent']);

        return view('pages.parent.create');
    }

    /**
     * Display the specified resource.
     *
     *
     * @throws AuthorizationException
     */
    public function show(User $parent): View
    {
        $this->authorize('view', [$parent, 'parent']);
        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.show', compact('parent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @throws AuthorizationException
     */
    public function edit(User $parent): View
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.edit', compact('parent'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws AuthorizationException
     */
    public function destroy(User $parent): RedirectResponse
    {
        $this->authorize('delete', [$parent, 'parent']);
        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');
        $this->parentService->deleteParent($parent);

        return back()->with('success', 'Parent Deleted Successfully');
    }

    /**
     * View for assigning students to parent.
     */
    public function assignStudentsView(User $parent)
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.assign-students', compact('parent'));
    }
}
