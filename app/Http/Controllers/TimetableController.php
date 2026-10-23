<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Services\Timetable\PersonalWeek;
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

    public function index(): View
    {
        return view('pages.timetable.index');
    }

    public function create(): View
    {
        return view('pages.timetable.create');
    }

    /**
     * Show the signed-in teacher every lesson they teach this week.
     *
     * On a campus that teaches separate subjects, a teacher's lessons sit on
     * several timetables: the sections they teach and the subjects that meet
     * at their own times.
     */
    public function week(PersonalWeek $week): View
    {
        $this->authorize('viewAny', Timetable::class);

        $timetables = $week->timetablesOfTeacher(auth()->user(), (int) current_school_id());

        return view('pages.timetable.week', [
            'timetables' => $timetables,
            'days' => $week->of($timetables, auth()->user(), lessonsOnly: true),
        ]);
    }

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

    public function edit(Timetable $timetable): View
    {
        return view('pages.timetable.edit', compact('timetable'));
    }

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
