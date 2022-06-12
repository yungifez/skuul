<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;
use App\Models\Section;
use App\Services\Section\SectionService;

class SectionController extends Controller
{
    public $section;

    public function __construct(SectionService $section)
    {
        $this->section = $section;
        $this->authorizeResource(Section::class, 'section');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.section.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.section.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SectionStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SectionStoreRequest $request)
    {
        $data = $request->except('_token');
        $this->section->createSection($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param Section $section
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        return view('pages.section.show', compact('section'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Section $section
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        $data['section'] = $section;

        return view('pages.section.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SectionUpdateRequest $request
     * @param Section              $section
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SectionUpdateRequest $request, Section $section)
    {
        $data = $request->except('_token', '_method');

        $this->section->updateSection($section, $request);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Section $section
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section $section)
    {
        $this->section->deleteSection($section);

        return back();
    }
}
