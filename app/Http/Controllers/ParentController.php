<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignStudentRequest;
use App\Models\User;
use App\Services\Parent\ParentService;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    /**
     * ParentService variable.
     */
    public ParentService $parent;

    public function __construct(ParentService $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', [User::class, 'parent']);

        return view('pages.parent.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [User::class, 'parent']);

        return view('pages.parent.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', [User::class, 'parent']);
        $this->parent->createParent($request);

        return back()->with('success', 'Parent Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $parent)
    {
        $this->authorize('view', [$parent, 'parent']);
        $this->parent->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.show', compact('parent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $parent)
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parent->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.edit', compact('parent'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $parent)
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parent->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');
        $this->parent->updateParent($parent, $request->except('_token', '_method'));

        return back()->with('success', 'Parent Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $parent)
    {
        $this->authorize('delete', [$parent, 'parent']);
        $this->parent->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');
        $this->parent->deleteParent($parent);

        return back()->with('success', 'Parent Deleted Successfully');
    }

    /**
     * View for assigning students to parent.
     *
     * @return \Illuminate\Http\Response
     */
    public function assignStudentsView(User $parent)
    {
        $this->parent->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');

        return view('pages.parent.assign-students', compact('parent'));
    }

    /**
     * Undocumented function.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignStudent(AssignStudentRequest $request, User $parent)
    {
        $this->parent->user->verifyUserIsOfRoleElseNotFound($parent, 'parent');
        $student = $request->student_id;
        //set to true if null
        $request->assign == null ? $assign = true : $assign = $request->assign;
        $this->parent->assignStudentToParent($parent, $student, $assign);

        if ($assign == false) {
            $message = 'Student successfully removed from parent';
        } else {
            $message = 'Student successfully assigned to parent';
        }

        return back()->with('success', $message);
    }
}
