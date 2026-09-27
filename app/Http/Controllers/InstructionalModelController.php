<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\View\View;

/**
 * The way a campus teaches an academic cycle.
 *
 * The choice sits with the cycle it belongs to, so staff answer it while they
 * set the cycle up rather than on a settings page of its own. The
 * ManageInstructionalModel component records every change.
 */
class InstructionalModelController extends Controller
{
    /**
     * Show the one question that sets up teaching for the cycle.
     */
    public function edit(AcademicYear $academicYear): View
    {
        $this->authorize('viewInstructionalModel', $academicYear);

        return view('pages.instructional-model.edit', ['academicYear' => $academicYear]);
    }
}
