<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * @var App\Services\Exam\ExamReordService
     */
    protected $examRecordService;

    /**
     * @var App\Services\Exam\ExamSlotService
     */
    protected $examSlotService;

    public function __construct(ExamRecordService $examRecordService, ExamSlotService $examSlotService)
    {
        $this->examRecordService = $examRecordService;
        $this->examSlotService = $examSlotService;
    }

    /**
     * Get all exams in a semester.
     *
     * @param int $semester_id
     *
     * @return void
     */
    public function getAllExamsInSemester(int $semester_id)
    {
        return Exam::where('semester_id', $semester_id)->get();
    }

    /**
     * Get active exams in a semester.
     *
     * @param int $semester_id
     *
     * @return void
     */
    public function getActiveExamsInSemester(int $semester_id)
    {
        return Exam::where(['semester_id'=> $semester_id, 'active' => true])->get();
    }

    /**
     * get an exam by it's id.
     *
     * @param int $id
     *
     * @return App\Models\Exam
     */
    public function getExamById(int $id)
    {
        return Exam::find($id);
    }

    /**
     * Create exam in semester.
     *
     * @param array|object $records
     *
     * @return void
     */
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
        session()->flash('success', 'Exam created successfully');
    }

    /**
     * Update an exam.
     *
     * @param Exam         $exam
     * @param array|object $records
     *
     * @return void
     */
    public function updateExam(Exam $exam, $records)
    {
        $exam->name = $records['name'];
        $exam->description = $records['description'];
        $exam->semester_id = $records['semester_id'];
        $exam->start_date = $records['start_date'];
        $exam->stop_date = $records['stop_date'];
        $exam->save();
        session()->flash('success', 'Exam updated successfully');
    }

    /**
     * set status of exam to active or inactive.
     *
     * @param Exam $exam
     * @param bool $status
     *
     * @return void
     */
    public function setExamStatus(Exam $exam, bool $status)
    {
        $exam->active = $status;
        $exam->save();
        session()->flash('success', 'Exam status changed successfully');
    }

    /**
     * Set result publish status for exam.
     *
     * @param Exam $exam
     * @param bool $status
     *
     * @return void
     */
    public function setPublishResultStatus(Exam $exam, bool $status)
    {
        $exam->publish_result = $status;
        $exam->save();

        return session()->flash('success', 'Result published status changed successfully');
    }

    /**
     * Delete exam.
     *
     * @param Exam $exam
     *
     * @return void
     */
    public function deleteExam(Exam $exam)
    {
        $exam->delete();

        return session()->flash('success', 'Exam deleted successfully');
    }

    /**
     * Calculate total marks attainale in each subjects for an exam.
     *
     * @param Exam $exam
     *
     * @return int
     */
    public function totalMarksAttainableInExamForSubject(Exam $exam)
    {
        $totalMarks = 0;
        foreach ($exam->examSlots as $examSlot) {
            $totalMarks += $examSlot->total_marks;
        }

        return $totalMarks;
    }

    /**
     * Calculate total marks attainale in each subjects accross all exams in a semester.
     *
     * @param Exam $exam
     *
     * @return int
     */
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

    /**
     * Calculate total marks attainale accross all subjects in an exam.
     *
     * @param Exam    $exam
     * @param User    $user
     * @param Subject $subject
     *
     * @return int
     */
    public function calculateStudentTotalMarksInSubject(Exam $exam, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInExamForSubject($exam, $user->id, $subject->id)->pluck('student_marks')->sum();
    }

    /**
     * Calculate total marks gotten by student in semester across all exams in a subject.
     *
     * @param Semester $semester
     * @param User     $user
     * @param Subject  $subject
     *
     * @return int
     */
    public function calculateStudentTotalMarkInSubjectForSemester(Semester $semester, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInSemesterForSubject($semester, $user->id, $subject->id)->pluck('student_marks')->sum();
    }
}
