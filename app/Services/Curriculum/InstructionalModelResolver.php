<?php

namespace App\Services\Curriculum;

use App\Enums\InstructionalModel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelSetting;
use App\Models\School;

/**
 * Say how a campus teaches one academic cycle.
 *
 * A campus that never chose reads as `InstructionalModel::default()`, so
 * every cycle recorded before the setting existed keeps working. Nothing here
 * writes: `App\Actions\Curriculum\SetInstructionalModel` is the way in.
 *
 * Answers are kept for the request. A screen that lists cycles calls
 * `preloadForSchool()` first and then reads every row without a query of its
 * own.
 */
class InstructionalModelResolver
{
    /**
     * The settings already read during this request, null included.
     *
     * @var array<string, InstructionalModelSetting|null>
     */
    private array $settings = [];

    /**
     * The campuses whose settings were all read at once.
     *
     * @var array<int, bool>
     */
    private array $preloadedSchools = [];

    /**
     * Get the model the campus teaches the cycle with.
     */
    public function for(AcademicYear|int|null $academicYear = null, School|int|null $school = null): InstructionalModel
    {
        $setting = $this->settingFor($academicYear, $school);

        return $setting === null ? InstructionalModel::default() : $setting->model;
    }

    /**
     * Get the saved choice, when the campus made one.
     */
    public function settingFor(AcademicYear|int|null $academicYear = null, School|int|null $school = null): ?InstructionalModelSetting
    {
        $academicYearId = $this->academicYearId($academicYear);
        $schoolId = $this->schoolId($school, $academicYear);

        if ($academicYearId === null || $schoolId === null) {
            return null;
        }

        $key = "$schoolId:$academicYearId";

        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }

        if (isset($this->preloadedSchools[$schoolId])) {
            return null;
        }

        return $this->settings[$key] = InstructionalModelSetting::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->first();
    }

    /**
     * Read every choice of one campus in a single query.
     *
     * A list of cycles asks this once, so the rows below it cost nothing.
     */
    public function preloadForSchool(School|int|null $school = null): void
    {
        $schoolId = $this->schoolId($school, null);

        if ($schoolId === null || isset($this->preloadedSchools[$schoolId])) {
            return;
        }

        InstructionalModelSetting::where('school_id', $schoolId)
            ->get()
            ->each(function (InstructionalModelSetting $setting) use ($schoolId): void {
                $this->settings["$schoolId:$setting->academic_year_id"] = $setting;
            });

        $this->preloadedSchools[$schoolId] = true;
    }

    /**
     * Check if the campus chose a model for the cycle itself.
     */
    public function isChosen(AcademicYear|int|null $academicYear = null, School|int|null $school = null): bool
    {
        return $this->settingFor($academicYear, $school) !== null;
    }

    /**
     * Forget what was read, after a setting changed.
     */
    public function forget(): void
    {
        $this->settings = [];
        $this->preloadedSchools = [];
    }

    /**
     * Read the cycle out of what the caller gave.
     */
    private function academicYearId(AcademicYear|int|null $academicYear): ?int
    {
        if ($academicYear instanceof AcademicYear) {
            return $academicYear->id;
        }

        return $academicYear ?? current_academic_year_id();
    }

    /**
     * Read the campus out of what the caller gave, or out of the cycle.
     */
    private function schoolId(School|int|null $school, AcademicYear|int|null $academicYear): ?int
    {
        if ($school instanceof School) {
            return $school->id;
        }

        if ($school !== null) {
            return $school;
        }

        if ($academicYear instanceof AcademicYear) {
            return $academicYear->school_id;
        }

        return current_school_id();
    }
}
