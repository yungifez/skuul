<?php

namespace App\Traits;

use App\Models\ClassGroup;
use App\Models\ExamRecord;
use App\Models\GradeSystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Mark tabulation traits.
 */
trait MarkTabulationTrait
{
    /**
     * Highest amount of marks a student can get.
     */
    public int $totalMarksAttainableInEachSubject;

    /**
     * A collection of all subjects used in tabulation creation.
     *
     * @var Collection<Subjects>
     */
    public Collection $subjects;

    public Collection $students;

    /**
     * @param Collection<int, \App\Models\Subject>  $subjects
     * @param Collection<int, \App\Models\Student>  $students
     * @param Collection<int, \App\Models\ExamSlot> $examSlots
     *
     * @return Collection
     */
    public function tabulateMarks(ClassGroup $classGroup, Collection|SupportCollection $subjects, Collection|SupportCollection $students, Collection|SupportCollection $examSlots)
    {
        //create tabulation container variable
        $tabulatedRecords = [];

        //get all relevant exam records
        $examRecords = ExamRecord::whereIn('subject_id', $subjects->pluck('id'))->whereIn('user_id', $students->pluck('id'))->get();

        //get all grades in class group
        $grades = GradeSystem::where('class_group_id', $classGroup->id)->get();
        $totalMarksAttainableInEachSubject = $examSlots->sum(['total_marks']);

        //set public variables
        $this->totalMarksAttainableInEachSubject = $totalMarksAttainableInEachSubject;
        $this->subjects = $subjects;
        $this->students = $students;

        //eager load relevant resources
        $students->load('studentRecord');

        foreach ($students as $student) {
            //array to hold tabulation values for each student
            $totalSubjectMarks = [];

            //set student name and admission number
            $tabulatedRecords[$student->id]['student_name'] = $student->name;
            $tabulatedRecords[$student->id]['admission_number'] = $student->studentRecord->admission_number;

            //loop through all subjects and add all marks
            foreach ($subjects as $subject) {
                $tabulatedRecords[$student->id]['student_marks'][$subject->id] = $examRecords->where('user_id', $student->id)->whereIn('exam_slot_id', $examSlots->pluck('id'))->where('subject_id', $subject->id)->pluck('student_marks')->sum();

                //array used for calculating total marks
                $totalSubjectMarks[] = $tabulatedRecords[$student->id]['student_marks'][$subject->id];
            }

            //turned to object
            $totalSubjectMarks = collect($totalSubjectMarks)->sum();

            //set total from summing each subject
            $tabulatedRecords[$student->id]['total'] = $totalSubjectMarks;

            //calculated percentage
            $totalMarks = $totalMarksAttainableInEachSubject * $subjects->count();

            //make sure total marks is not 0
            $totalMarks = $totalMarks ? $totalMarks : 1;
            $tabulatedRecords[$student->id]['percent'] = ceil(($totalSubjectMarks / $totalMarks) * 100);
            $percentage = $tabulatedRecords[$student->id]['percent'];
            $grade = $grades->where('grade_from', '<=', $percentage)->where('grade_till', '>=', $percentage)->first();

            //get appropriate grade
            $tabulatedRecords[$student->id]['grade'] = $grade ? $grade->name : 'No Grade';
        }

        return collect($tabulatedRecords);
    }
}
