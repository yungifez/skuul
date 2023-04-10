<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignStudentRequest;
use App\Models\User;
use App\Services\Parent\ParentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', [User::class, 'parent']);
        $this->parentService->createParent($request->except('_token'));

        return back()->with('success', 'Parent Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $parent): View
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.edit', compact('parent'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $parent): RedirectResponse
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');
        $this->parentService->updateParent($parent, $request->except('_token', '_method'));

        return back()->with('success', 'Parent Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
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

    /**
     *  Assign or deassign student to parent.
     */
    public function assignStudent(AssignStudentRequest $request, User $parent): RedirectResponse
    {
        $this->authorize('update', [$parent, 'parent']);

        $this->parentService->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');
        $student = $request->student_id;
        //set to true if null
        $request->assign == null ? $assign = true : $assign = $request->assign;
        $this->parentService->assignStudentToParent($parent, $student, $assign);

        if ($assign == false) {
            $message = 'Student successfully removed from parent';
        } else {
            $message = 'Student successfully assigned to parent';
        }

        return back()->with('success', $message);
    }
}
