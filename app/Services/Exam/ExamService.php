<?php

namespace App\Services\Exam;

use App\Exceptions\EmptyRecordsException;
use App\Models\Exam;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ExamService
{
    /**
     * @var ExamRecordService
     */
    protected $examRecordService;

    /**
     * @var ExamSlotService
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
     *
     * @return Collection
     */
    public function getAllExamsInSemester(int $semester_id)
    {
        return Exam::where('semester_id', $semester_id)->get();
    }

    /**
     * Get active exams in a semester.
     *
     *
     * @return mixed
     */
    public function getActiveExamsInSemester(int $semester_id)
    {
        return Exam::where(['semester_id' => $semester_id, 'active' => true])->get();
    }

    /**
     * get an exam by it's id.
     *
     *
     * @return \App\Models\Exam
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
     * @return Exam
     */
    public function createExam($records)
    {
        $exam = Exam::create([
            'name'        => $records['name'],
            'description' => $records['description'],
            'semester_id' => $records['semester_id'],
            'start_date'  => $records['start_date'],
            'stop_date'   => $records['stop_date'],
        ]);

        return $exam;
    }

    /**
     * Update an exam.
     *
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
    }

    /**
     * set if exam is active or not .
     *
     *
     * @return void
     */
    public function setExamActiveStatus(Exam $exam, bool $active)
    {
        $exam->active = $active;
        $exam->save();
    }

    /**
     * Set result publish status for exam.
     *
     *@throws
     *
     * @return void
     */
    public function setPublishResultStatus(Exam $exam, bool $status)
    {
        if ($exam->examSlots()->count() <= 0 && $status == 1) {
            throw new EmptyRecordsException('Cannot publish result for exam without exam slots', 1);
        }

        $exam->publish_result = $status;
        $exam->save();
    }

    /**
     * Delete exam.
     *
     *
     * @return void
     */
    public function deleteExam(Exam $exam)
    {
        $exam->delete();
    }

    /**
     * Calculate total marks attainable in each subject across all exams in a semester.
     *
     * @param Exam $exam
     *
     * @return int
     */
    public function totalMarksAttainableInSemesterForSubject(Semester $semester)
    {
        $totalMarks = 0;
        $exams = $semester->exams->load('examSlots');
        //get all exam slots in exams
        foreach ($exams as $exam) {
            $totalMarks += $exam->examSlots->sum(['total_marks']);
        }

        return $totalMarks;
    }

    /**
     * Calculate total marks attainale accross all subjects in an exam.
     *
     *
     * @return int
     */
    public function calculateStudentTotalMarksInSubject(Exam $exam, User $user, Subject $subject)
    {
        return $this->examRecordService->getAllUserExamRecordInExamForSubject($exam, $user->id, $subject->id)->pluck('student_marks')->sum();
    }
}
