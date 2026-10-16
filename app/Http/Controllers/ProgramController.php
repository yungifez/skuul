<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Contracts\View\View;

/**
 * A named activity a student takes part in.
 *
 * Taking part never touches enrollment. A student who leaves a club is still
 * a student.
 */
class ProgramController extends Controller
{
    /**
     * Show the programmes this school runs.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Program::class);

        return view('pages.program.index');
    }

    /**
     * Show the form that opens a programme.
     */
    public function create(): View
    {
        $this->authorize('create', Program::class);

        return view('pages.program.create');
    }

    /**
     * Show one programme and who takes part.
     */
    public function show(Program $program): View
    {
        $this->authorize('view', $program);

        return view('pages.program.show', ['program' => $program]);
    }
}
