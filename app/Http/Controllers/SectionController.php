<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\Section\SectionService;
use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;

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
     */
    public function index(): View
    {
        return view('pages.section.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.section.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SectionStoreRequest $request
     */
    public function store(SectionStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->section->createSection($data);

        return back()->with('success', __('Section created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param Section $section
     */
    public function show(Section $section): View
    {
        return view('pages.section.show', compact('section'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Section $section
     */
    public function edit(Section $section): View
    {
        $data['section'] = $section;

        return view('pages.section.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SectionUpdateRequest $request
     * @param Section              $section
     */
    public function update(SectionUpdateRequest $request, Section $section): RedirectResponse
    {
        $data = $request->except('_token', '_method');

        $this->section->updateSection($section, $request);

        return back()->with('success', __('Section updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Section $section
     */
    public function destroy(Section $section): RedirectResponse
    {
        $this->section->deleteSection($section);

        return back()->with('success', __('Section deleted successfully'));
    }
}
