<?php

namespace App\Services\Semester;

use App\Enums\AcademicPeriodType;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SemesterService
{
    /**
     * Get all semesters in school.
     *
     * @return Collection
     */
    public function getAllSemesters()
    {
        return Semester::inSchool()->get();
    }

    /**
     * Get all semesters in academic year.
     *
     *
     * @return Collection
     */
    public function getAllSemestersInAcademicYear(int $academicYear)
    {
        return $this->getAllSemesters()->where('academic_year_id', $academicYear);
    }

    /**
     * Get semester by Id.
     *
     *
     * @return Semester
     */
    public function getSemesterById(int $id)
    {
        return Semester::find($id);
    }

    /**
     * Create a new semester.
     *
     * @param mixed $data
     *
     * @return Semester
     */
    public function createSemester($data)
    {
        $academicYear = academic_period_context()->academicYearOrFail();

        $startsOn = $this->date($data['starts_on'] ?? null);
        $endsOn = $this->date($data['ends_on'] ?? null);

        $this->failIfDatesDoNotFit($academicYear, $startsOn, $endsOn);

        $attributes = [
            'name'             => $data['name'],
            'type'             => $data['type'] ?? AcademicPeriodType::Semester->value,
            'starts_on'        => $startsOn,
            'ends_on'          => $endsOn,
            'school_id'        => current_school_id(),
            'academic_year_id' => $academicYear->id,
        ];

        // The model gives the period the next place when nobody names one.
        if (isset($data['position'])) {
            $attributes['position'] = $data['position'];
        }

        return Semester::create($attributes);
    }

    /**
     * Set current semester.
     *
     *
     *
     * @throws InvalidValueException
     *
     * @return void
     */
    public function setSemester(Semester $semester)
    {
        $academicYear = academic_period_context()->academicYear();

        if ($academicYear === null || $semester->academic_year_id !== $academicYear->id) {
            throw new InvalidValueException('Semester not in current academic year');
        }

        academic_period_context()->setSemester($semester);
    }

    /**
     * Semester service.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function updateSemester(Semester $semester, $data)
    {
        $startsOn = array_key_exists('starts_on', $data) ? $this->date($data['starts_on']) : $semester->starts_on;
        $endsOn = array_key_exists('ends_on', $data) ? $this->date($data['ends_on']) : $semester->ends_on;

        $this->failIfDatesDoNotFit($semester->academicYear, $startsOn, $endsOn, $semester);

        $semester->name = $data['name'];
        $semester->type = $data['type'] ?? $semester->type;
        $semester->position = $data['position'] ?? $semester->position;
        $semester->starts_on = $startsOn;
        $semester->ends_on = $endsOn;
        $semester->save();
    }

    /**
     * Read a date the form sent.
     */
    private function date(mixed $value): ?Carbon
    {
        return $value === null || $value === '' ? null : Carbon::parse($value)->startOfDay();
    }

    /**
     * Check that the dates make a period and that no other period holds them.
     *
     * Two periods that share a day make every date-based question ambiguous,
     * so the overlap is refused when it is written.
     *
     * @throws InvalidValueException
     */
    private function failIfDatesDoNotFit(?AcademicYear $academicYear, ?Carbon $startsOn, ?Carbon $endsOn, ?Semester $ignore = null): void
    {
        if ($startsOn === null && $endsOn === null) {
            return;
        }

        if ($startsOn === null || $endsOn === null) {
            throw new InvalidValueException('Give the period both a start date and an end date.');
        }

        if ($endsOn->lessThan($startsOn)) {
            throw new InvalidValueException('The period cannot end before it starts.');
        }

        if ($academicYear === null) {
            return;
        }

        $overlapping = $academicYear->semesters()
            ->when($ignore !== null, fn ($query) => $query->whereKeyNot($ignore->id))
            ->whereNotNull('starts_on')
            ->whereNotNull('ends_on')
            ->where('starts_on', '<=', $endsOn)
            ->where('ends_on', '>=', $startsOn)
            ->first();

        if ($overlapping !== null) {
            throw new InvalidValueException("These dates overlap $overlapping->name.");
        }
    }

    /**
     * Delete Semester.
     *
     *
     * @return void
     */
    public function deleteSemester(Semester $semester)
    {
        $semester->delete();
    }
}
