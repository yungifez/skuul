<?php

namespace App\Http\Controllers;

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
        $this->authorize('view', $timetable);

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
