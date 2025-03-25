<?php

namespace App\Services\AcademicYear;

use App\Models\AcademicYear;
use App\Services\School\SchoolService;
use Illuminate\Database\Eloquent\Collection;

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
     */
    public function getAllAcademicYears(): Collection|static
    {
        return AcademicYear::where('school_id', auth()->user()->school_id)->get();
    }

    /**
     * Get academic year by Id.
     *
     * @param int $id
     */
    public function getAcademicYearById($id): AcademicYear
    {
        return AcademicYear::find($id);
    }

    /**
     * Create academic year.
     *
     * @param array|Collection $records
     */
    public function createAcademicYear($records): AcademicYear
    {
        $records['school_id'] = auth()->user()->school_id;
        $academicYear = AcademicYear::create($records);

        return $academicYear;
    }

    /**
     * Update Academic Year.
     *
     * @param array|Collection $records
     */
    public function updateAcademicYear(AcademicYear $academicYear, $records): AcademicYear
    {
        $academicYear->start_year = $records['start_year'];
        $academicYear->stop_year = $records['stop_year'];
        $academicYear->save();

        return $academicYear;
    }

    /**
     * Delete an academic year.
     */
    public function deleteAcademicYear(AcademicYear $academicYear): ?bool
    {
        return $academicYear->delete();
    }

    /**
     * Set academic year as current.one in school.
     *
     * @param int $academicYearId
     * @param int $schoolId
     */
    public function setAcademicYear($academicYearId, $schoolId = null): bool
    {
        $academicYear = AcademicYear::find($academicYearId);
        if (!isset($schoolId)) {
            $schoolId = auth()->user()->school_id;
        }
        $school = $this->schoolService->getSchoolById($schoolId);
        $school->academic_year_id = $academicYearId;
        //set semester id to first semester or null
        $school->semester_id = $academicYear->semesters?->first()->id ?? $school->academicYear->semesters()->create([
            'name'      => 'First',
            'school_id' => auth()->user()->school_id,
        ])->id;

        return $school->save();
    }
}
