<?php

namespace App\Services\Exam;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\EmptyRecordsException;
use App\Models\AcademicPeriod;
use App\Models\Exam;
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

    public function __construct(ExamRecordService $examRecordService, ExamSlotService $examSlotService, private RecordAuditEvent $auditor)
    {
        $this->examRecordService = $examRecordService;
        $this->examSlotService = $examSlotService;
    }

    /**
     * Get all exams in an academic period.
     *
     *
     * @return Collection
     */
    public function getAllExamsInAcademicPeriod(int $academic_period_id)
    {
        return Exam::where('academic_period_id', $academic_period_id)->get();
    }

    /**
     * Get active exams in an academic period.
     *
     *
     * @return mixed
     */
    public function getActiveExamsInAcademicPeriod(int $academic_period_id)
    {
        return Exam::where(['academic_period_id' => $academic_period_id, 'active' => true])->get();
    }

    /**
     * get an exam by it's id.
     *
     *
     * @return Exam
     */
    public function getExamById(int $id)
    {
        return Exam::find($id);
    }

    /**
     * Create exam in academic period.
     *
     * @param  array|object  $records
     * @return Exam
     */
    public function createExam($records)
    {
        $exam = Exam::create([
            'name' => $records['name'],
            'description' => $records['description'],
            'academic_period_id' => $records['academic_period_id'],
            'start_date' => $records['start_date'],
            'stop_date' => $records['stop_date'],
        ]);

        return $exam;
    }

    /**
     * Update an exam.
     *
     * @param  array|object  $records
     * @return void
     */
    public function updateExam(Exam $exam, $records)
    {
        $exam->name = $records['name'];
        $exam->description = $records['description'];
        $exam->academic_period_id = $records['academic_period_id'];
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
     *
     *
     * @return void
     *
     * @throws EmptyRecordsException
     */
    public function setPublishResultStatus(Exam $exam, bool $status)
    {
        if ($exam->examSlots()->count() <= 0 && $status == 1) {
            throw new EmptyRecordsException('Cannot publish result for exam without exam slots', 1);
        }

        $wasPublished = (bool) $exam->publish_result;

        $exam->publish_result = $status;
        $exam->save();

        // Publication decides what students and parents can see, so record it.
        if ($wasPublished !== $status) {
            $this->auditor->record(
                $status ? AuditAction::ExamResultPublished : AuditAction::ExamResultUnpublished,
                $exam,
                ['exam' => $exam->name, 'academic_period_id' => $exam->academic_period_id],
            );
        }
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
     * Calculate total marks attainable in each subject across all exams in an academic period.
     *
     * @param  Exam  $exam
     * @return int
     */
    public function totalMarksAttainableInAcademicPeriodForSubject(AcademicPeriod $academicPeriod)
    {
        $totalMarks = 0;
        $exams = $academicPeriod->exams->load('examSlots');
        // get all exam slots in exams
        foreach ($exams as $exam) {
            $totalMarks += $exam->examSlots->sum('total_marks');
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
