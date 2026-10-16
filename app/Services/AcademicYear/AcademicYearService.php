<?php

namespace App\Services\AcademicYear;

use App\Enums\AcademicPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\UserAcademicPeriodPreference;
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
     * @param  int  $id
     */
    public function getAcademicYearById($id): AcademicYear
    {
        return AcademicYear::find($id);
    }

    /**
     * Create academic year.
     *
     * @param  array|Collection  $records
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
     * @param  array|Collection  $records
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
     * The choice belongs to the staff member, not to the school record. The
     * session still caches it for the current request, while the working
     * period preference persists it across sessions.
     *
     *
     * @throws InvalidValueException when the year belongs to another school
     */
    public function setAcademicYear(int $academicYearId, User $user): bool
    {
        $academicYear = academic_period_context()->allowedAcademicYear(school_context()->schoolOrFail(), $academicYearId);

        if ($academicYear === null) {
            throw new InvalidValueException('That academic year does not belong to this school');
        }

        if ($academicYear->status === AcademicPeriodStatus::Draft) {
            throw new InvalidValueException('Publish the school calendar before making it the working calendar.');
        }

        $academicPeriod = $academicYear->topLevelPeriods()->first();

        if ($academicPeriod === null) {
            throw new InvalidValueException('This school calendar has no reporting periods.');
        }

        $preference = UserAcademicPeriodPreference::firstOrCreate(
            [
                'user_id' => $user->id,
                'school_id' => current_school_id(),
                'academic_year_id' => $academicYear->id,
            ],
            ['academic_period_id' => $academicPeriod->id],
        );

        academic_period_context()->setAcademicYear($academicYear);
        academic_period_context()->setAcademicPeriod($preference->academicPeriod);

        return true;
    }

    /**
     * Set the academic year a school opens in by default.
     */
    public function setSchoolDefaultAcademicYear(AcademicYear $academicYear): bool
    {
        $school = $this->schoolService->getSchoolById($academicYear->school_id);
        $school->academic_year_id = $academicYear->id;
        $school->academic_period_id = $academicYear->academicPeriods()->orderBy('id')->first()?->id;

        return $school->save();
    }
}
