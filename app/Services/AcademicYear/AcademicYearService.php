<?php

namespace App\Services\AcademicYear;

use App\Exceptions\InvalidValueException;
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
        return AcademicYear::inSchool()->get();
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
        $records['school_id'] = current_school_id();
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
        $academicYear->starts_on = $records['starts_on'] ?? null;
        $academicYear->ends_on = $records['ends_on'] ?? null;
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
     * Set the academic year this person works in.
     *
     * The choice belongs to the request, not to the school record. A school
     * still keeps a default year for people who have not chosen one.
     *
     * @param int $academicYearId
     *
     * @throws InvalidValueException when the year belongs to another school
     */
    public function setAcademicYear($academicYearId): bool
    {
        $academicYear = academic_period_context()->allowedAcademicYear(school_context()->schoolOrFail(), $academicYearId);

        if ($academicYear === null) {
            throw new InvalidValueException('That academic year does not belong to this school');
        }

        academic_period_context()->setAcademicYear($academicYear);

        // A year always opens on a semester, so make the first one if it has none.
        $semester = $academicYear->semesters()->orderBy('id')->first()
            ?? $academicYear->semesters()->create([
                'name'      => 'First',
                'school_id' => $academicYear->school_id,
            ]);

        academic_period_context()->setSemester($semester);

        return true;
    }

    /**
     * Set the academic year a school opens in by default.
     */
    public function setSchoolDefaultAcademicYear(AcademicYear $academicYear): bool
    {
        $school = $this->schoolService->getSchoolById($academicYear->school_id);
        $school->academic_year_id = $academicYear->id;
        $school->semester_id = $academicYear->semesters()->orderBy('id')->first()?->id;

        return $school->save();
    }
}
