<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusService;

class SyllabusController extends Controller
{
    public function __construct(SyllabusService $syllabus)
    {
        $this->syllabus = $syllabus;
        $this->authorizeResource(Syllabus::class, 'syllabus');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.syllabus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.syllabus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreSyllabusRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSyllabusRequest $request)
    {
        $data = $request->except(['_token']);
        $this->syllabus->createSyllabus($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Syllabus $syllabus
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Syllabus $syllabus)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Syllabus $syllabus
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Syllabus $syllabus)
    {
        abort('404');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateSyllabusRequest $request
     * @param \App\Models\Syllabus                     $syllabus
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Syllabus $syllabus
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Syllabus $syllabus)
    {
        $this->syllabus->deleteSyllabus($syllabus);

        return back();
    }
}
