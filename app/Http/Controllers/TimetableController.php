<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use App\Http\Requests\TimetableStoreRequest;
use App\Services\Timetable\TimetableService;
use App\Http\Requests\TimetableUpdateRequest;

class TimetableController extends Controller
{
    public $timetable;

    public function __construct(TimetableService $timetable)
    {
        $this->timetable = $timetable;
        $this->authorizeResource(Timetable::Class,'timetable');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.timetable.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.timetable.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TimetableStoreRequest $request)
    {
        $data = $request->except('_token');
        $this->timetable->createTimetable($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function show(Timetable $timetable)
    {
       return view('pages.timetable.show',compact('timetable'));
    }

    /**
     * Print timetsable
     */
    public function print(Timetable $timetable)
    {
        $data['timetable'] = $timetable;
;
        return $this->timetable->printTimetable($data['timetable']->name, 'pages.timetable.print', $data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function edit(Timetable $timetable)
    {
        return view('pages.timetable.edit', compact('timetable'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function update(TimetableUpdateRequest $request, Timetable $timetable)
    {
        $data = $request->except('_token'. '_method');
        $this->timetable->updateTimetable($timetable, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable)
    {
        $this->timetable->deleteTimetable($timetable);

        return back();
    }

    //manage timetable
    public function manage(Timetable $timetable)
    {
        $this->authorize('update', $timetable);
        return view('pages.timetable.manage',compact('timetable'));
    }
}
