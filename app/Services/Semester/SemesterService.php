<?php

namespace App\Services\Semester;

use App\Exceptions\InvalidValueException;
use App\Models\Semester;

class SemesterService
{
    /**
     * Get all semesters in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSemesters()
    {
        return Semester::where(['school_id' => auth()->user()->school_id])->get();
    }

    /**
     * Get all semesters in academic year.
     *
     *
     * @return \Illuminate\Database\Eloquent\Collection
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
        $data['academic_year_id'] = auth()->user()->school->academicYear->id;
        $data['school_id'] = auth()->user()->school->id;
        $semester = Semester::create([
            'name'             => $data['name'],
            'school_id'        => $data['school_id'],
            'academic_year_id' => $data['academic_year_id'],
        ]);

        return $semester;
    }

    /**
     * Set current semester.
     *
     *
     * @throws InvalidValueException
     *
     * @return void
     */
    public function setSemester(Semester $semester)
    {
        $school = auth()->user()->school;
        if ($semester->academicYear->id != $school->academic_year_id) {
            throw new InvalidValueException('Semester not in current academic year');
        }
        $school->semester_id = $semester->id;
        $school->save();
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
        $semester->name = $data['name'];
        $semester->save();
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
