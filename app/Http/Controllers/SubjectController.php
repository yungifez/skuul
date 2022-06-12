<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectStoreRequest;
use App\Models\Subject;
use App\Services\Subject\SubjectService;

class SubjectController extends Controller
{
    public $subject;

    public function __construct(SubjectService $subject)
    {
        $this->subject = $subject;
        $this->authorizeResource(Subject::class, 'subject');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.subject.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.subject.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SubjectStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SubjectStoreRequest $request)
    {
        $data = $request->except(['_token']);

        $this->subject->createSubject($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param Subject $subject
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Subject $subject
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        $data['subject'] = $subject;

        return view('pages.subject.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SubjectStoreRequest $request
     * @param Subject             $subject
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SubjectStoreRequest $request, Subject $subject)
    {
        $data = $request->except(['_token', '_method']);

        $this->subject->updateSubject($subject, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subject $subject
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        $this->subject->deleteSubject($subject);

        return back();
    }
}
