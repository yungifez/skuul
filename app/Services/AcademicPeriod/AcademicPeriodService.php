<?php

namespace App\Services\AcademicPeriod;

use App\Enums\AcademicPeriodType;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\AcademicPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AcademicPeriodService
{
    /**
     * Get all academic periods in school.
     *
     * @return Collection
     */
    public function getAllAcademicPeriods()
    {
        return AcademicPeriod::inSchool()->get();
    }

    /**
     * Get all academic periods in academic year.
     *
     *
     * @return Collection
     */
    public function getAllAcademicPeriodsInAcademicYear(int $academicYear)
    {
        return $this->getAllAcademicPeriods()->where('academic_year_id', $academicYear);
    }

    /**
     * Get academic period by Id.
     *
     *
     * @return AcademicPeriod
     */
    public function getAcademicPeriodById(int $id)
    {
        return AcademicPeriod::find($id);
    }

    /**
     * Create a new academic period.
     *
     * @param mixed $data
     *
     * @return AcademicPeriod
     */
    public function createAcademicPeriod($data)
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

        return AcademicPeriod::create($attributes);
    }

    /**
     * Set current academic period.
     *
     *
     *
     * @throws InvalidValueException
     *
     * @return void
     */
    public function setAcademicPeriod(AcademicPeriod $academicPeriod)
    {
        $academicYear = academic_period_context()->academicYear();

        if ($academicYear === null || $academicPeriod->academic_year_id !== $academicYear->id) {
            throw new InvalidValueException('AcademicPeriod not in current academic year');
        }

        academic_period_context()->setAcademicPeriod($academicPeriod);
    }

    /**
     * AcademicPeriod service.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function updateAcademicPeriod(AcademicPeriod $academicPeriod, $data)
    {
        $startsOn = array_key_exists('starts_on', $data) ? $this->date($data['starts_on']) : $academicPeriod->starts_on;
        $endsOn = array_key_exists('ends_on', $data) ? $this->date($data['ends_on']) : $academicPeriod->ends_on;

        $this->failIfDatesDoNotFit($academicPeriod->academicYear, $startsOn, $endsOn, $academicPeriod);

        $academicPeriod->name = $data['name'];
        $academicPeriod->type = $data['type'] ?? $academicPeriod->type;
        $academicPeriod->position = $data['position'] ?? $academicPeriod->position;
        $academicPeriod->starts_on = $startsOn;
        $academicPeriod->ends_on = $endsOn;
        $academicPeriod->save();
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
    private function failIfDatesDoNotFit(?AcademicYear $academicYear, ?Carbon $startsOn, ?Carbon $endsOn, ?AcademicPeriod $ignore = null): void
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

        $overlapping = $academicYear->academicPeriods()
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
     * Delete AcademicPeriod.
     *
     *
     * @return void
     */
    public function deleteAcademicPeriod(AcademicPeriod $academicPeriod)
    {
        $academicPeriod->delete();
    }
}
