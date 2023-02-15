<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SyllabusController extends Controller
{
    public $syllabus;

    public function __construct(SyllabusService $syllabus)
    {
        $this->syllabus = $syllabus;
        $this->authorizeResource(Syllabus::class, 'syllabus');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.syllabus.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.syllabus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSyllabusRequest $request)
    {
        $data = $request->except(['_token']);
        $this->syllabus->createSyllabus($data);

        return back()->with('success', 'Successfully created Syllabus');
    }

    /**
     * Display the specified resource.
     */
    public function show(Syllabus $syllabus): View
    {
        return view('pages.syllabus.show', compact('syllabus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Syllabus $syllabus): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus): Response
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Syllabus $syllabus): RedirectResponse
    {
        $this->syllabus->deleteSyllabus($syllabus);

        return back()->with('success', 'Successfully deleted Syllabus');
    }
}
