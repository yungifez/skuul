<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamService
{
    protected $examRecordService;
    protected $examSlotService;

    public function __construct(ExamRecordService $examRecordService, ExamSlotService $examSlotService)
    {
        $this->examRecordService = $examRecordService;
        $this->examSlotService = $examSlotService;
    }

    public function getAllExamsInSemester($semester_id)
    {
        return Exam::where('semester_id', $semester_id)->get();
    }

    public function getActiveExamsInSemester($semester_id)
    {
        return Exam::where(['semester_id'=> $semester_id, 'active' => true])->get();
    }

    //get exam by id

    public function getExamById($id)
    {
        return Exam::find($id);
    }

    public function createExam($records)
    {
        DB::transaction(function () use ($records) {
            $exam = Exam::create([
                'name'        => $records['name'],
                'description' => $records['description'],
                'semester_id' => $records['semester_id'],
                'start_date'  => $records['start_date'],
                'stop_date'   => $records['stop_date'],
            ]);
        });

        return session()->flash('success', 'Exam created successfully');
    }

    public function updateExam(Exam $exam, $records)
    {
        $exam->name = $records['name'];
        $exam->description = $records['description'];
        $exam->semester_id = $records['semester_id'];
        $exam->start_date = $records['start_date'];
        $exam->stop_date = $records['stop_date'];
        $exam->save();

        return session()->flash('success', 'Exam updated successfully');
    }

    //set exam active or inactive
    public function setExamStatus(Exam $exam, bool $status)
    {
        $exam->active = $status;
        $exam->save();

        return session()->flash('success', 'Exam status changed successfully');
    }

    public function deleteExam(Exam $exam)
    {
        $exam->delete();

        return session()->flash('success', 'Exam deleted successfully');
    }

    //calculate total marks attainable for each subject in exam
    public function totalMarksAttainableInExamForSubject(Exam $exam)
    {
        $totalMarks = 0;
        foreach ($exam->examSlots as $examSlot) {
            $totalMarks += $examSlot->total_marks;
        }

        return $totalMarks;
    }

    //calculate total marks attainable for each subject in semester
    public function totalMarksAttainableInSemesterForSubject(Semester $semester)
    {
        $totalMarks = 0;
        $exams = $semester->exams;
        //get all exam slots in exams
        foreach ($exams as $exam) {
            $totalMarks += $exam->examSlots->sum(['total_marks']);
        }

        return $totalMarks;
    }

    //calculate total mark gotten in subject for exam

    public function calculateStudentTotalMarksInSubject(Exam $exam, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInExamForSubject($exam, $user->id, $subject->id)->pluck('student_marks')->sum();
    }

    //calculate student mark gotten in semester

    public function calculateStudentTotalMarkInSubjectForSemester(Semester $semester, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInSemesterForSubject($semester, $user->id, $subject->id)->pluck('student_marks')->sum();
    }
}
