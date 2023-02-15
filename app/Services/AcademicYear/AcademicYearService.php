<?php

namespace App\Services\AcademicYear;

use App\Models\AcademicYear;
use App\Services\School\SchoolService;

class AcademicYearService
{
    /**
     * @var SchoolService
     */
    public $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    /**
     * Get all academic years.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllAcademicYears()
    {
        return AcademicYear::where('school_id', auth()->user()->school_id)->get();
    }

    /**
     * Get academic year by Id.
     *
     *@param  int  $id
     *
     * @return App\Models\AcademicYear
     */
    public function getAcademicYearById($id)
    {
        return AcademicYear::where('id', $id)->first();
    }

    /**
     * Create academic year.
     *
     * @param array|Collection $records
     *
     * @return AcademicYear
     */
    public function createAcademicYear($records)
    {
        $records['school_id'] = auth()->user()->school_id;
        $academicYear = AcademicYear::create($records);
    }

    /**
     * Update Academic Year.
     *
     * @param array|Collection $records
     *
     * @return void
     */
    public function updateAcademicYear(AcademicYear $academicYear, $records)
    {
        $academicYear->start_year = $records['start_year'];
        $academicYear->stop_year = $records['stop_year'];
        $academicYear->save();
    }

    /**
     * Delete an academic year.
     *
     *
     * @return void
     */
    public function deleteAcademicYear(AcademicYear $academicYear)
    {
        $academicYear->delete();
    }

    /**
     * Set academic year as current.one in school.
     *
     * @param int $academicYearId
     * @param int $schoolId
     *
     * @return void
     */
    public function setAcademicYear($academicYearId, $schoolId = null)
    {
        if (!isset($schoolId)) {
            $schoolId = auth()->user()->school_id;
        }
        $school = $this->schoolService->getSchoolById($schoolId);
        $school->academic_year_id = $academicYearId;
        //set semester id to null
        $school->semester_id = null;
        $school->save();
    }
}
