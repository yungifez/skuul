<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Parent\ParentService;

class ParentController extends Controller
{
 /**
     * ParentService variable
     *
     * @var \App\Services\Parent\ParentService
     * 
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', [User::class, 'parent']);
        $this->parent->createParent($request);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param User $parent
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $parent)
    {
        $this->authorize('view', [$parent, 'parent']);

        return view('pages.parent.show', compact('parent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $parent
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $parent)
    {
        $this->authorize('update', [$parent, 'parent']);

        return view('pages.parent.edit', compact('parent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User                     $parent
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $parent)
    {
        $this->authorize('update', [$parent, 'parent']);
        $this->parent->updateParent($parent, $request->except('_token', '_method'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $parent
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $parent)
    {
        $this->authorize('delete', [$parent, 'parent']);
        $this->parent->deleteParent($parent);

        return back();
    }
}
