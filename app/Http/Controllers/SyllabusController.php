<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Services\Syllabus\SyllabusService;
use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;

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
     *
     * @param \App\Http\Requests\StoreSyllabusRequest $request
    */
    public function store(StoreSyllabusRequest $request)
    {
        $data = $request->except(['_token']);
        $this->syllabus->createSyllabus($data);

        return back()->with('success', 'Successfully created Syllabus');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Syllabus $syllabus
    */
    public function show(Syllabus $syllabus): View
    {
        return view('pages.syllabus.show', compact('syllabus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Syllabus $syllabus
    */
    public function edit(Syllabus $syllabus): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateSyllabusRequest $request
     * @param \App\Models\Syllabus                     $syllabus
    */
    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus): Response
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Syllabus $syllabus
    */
    public function destroy(Syllabus $syllabus): RedirectResponse
    {
        $this->syllabus->deleteSyllabus($syllabus);

        return back()->with('success', 'Successfully deleted Syllabus');
    }
}
