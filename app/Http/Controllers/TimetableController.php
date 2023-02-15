<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimetableStoreRequest;
use App\Http\Requests\TimetableUpdateRequest;
use App\Models\Timetable;
use App\Services\Timetable\TimetableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public $timetableService;

    public function __construct(TimetableService $timetableService)
    {
        $this->timetableService = $timetableService;
        $this->authorizeResource(Timetable::class, 'timetable');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.timetable.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.timetable.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimetableStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $data['semester_id'] = auth()->user()->school->semester_id;

        $this->timetableService->createTimetable($data);

        return back()->with('success', 'Timetable created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Timetable $timetable): View
    {
        return view('pages.timetable.show', compact('timetable'));
    }

    /**
     * Print timetable.
     */
    public function print(Timetable $timetable): Response
    {
        $data['timetable'] = $timetable;

        return $this->timetableService->printTimetable($data['timetable']->name, 'pages.timetable.print', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timetable $timetable): View
    {
        return view('pages.timetable.edit', compact('timetable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimetableUpdateRequest $request, Timetable $timetable): RedirectResponse
    {
        $data = $request->except('_token'.'_method');
        $this->timetableService->updateTimetable($timetable, $data);

        return back()->with('success', 'Timetable updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timetable $timetable): RedirectResponse
    {
        $this->timetableService->deleteTimetable($timetable);

        return back()->with('success', 'Timetable deleted successfully');
    }

    /**
     * Manage Timetable.
     */
    public function manage(Timetable $timetable): View
    {
        $this->authorize('update', $timetable);

        return view('pages.timetable.manage', compact('timetable'));
    }
}
