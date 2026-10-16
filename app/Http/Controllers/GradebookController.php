<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function __construct(private CourseOfferingRoster $roster) {}

    /**
     * Show gradebooks for the selected year and period.
     */
    public function index(): View
    {
        $this->authorize('viewAnyGradebooks', CourseOffering::class);

        return view('pages.course-offering.gradebooks');
    }

    /**
     * Show the one-screen gradebook for an exact course offering.
     */
    public function show(CourseOffering $courseOffering): View
    {
        $this->authorize('viewGradebook', $courseOffering);

        $courseOffering->load([
            'academicLevel:id,name',
            'academicPeriod:id,name,label,status',
            'academicYear:id,start_year,stop_year',
            'subject:id,name,short_name',
        ]);

        return view('pages.course-offering.gradebook', [
            'courseOffering' => $courseOffering,
            'learnerCount' => $this->roster->students($courseOffering)->count(),
        ]);
    }
}
