<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\Semester;
use App\Services\Subject\SubjectService;
use Illuminate\Support\Facades\DB;

class ExamRecordService
{
    /**
     * @var App\Services\Exam\ExamSlotService
     */
    protected ExamSlotService $examSlot;
    /**
     * Subject service class.
     *
     * @var App\Services\SubjectService
     */
    protected SubjectService $subject;

    public function __construct(ExamSlotService $examSlot, SubjectService $subject)
    {
        $this->examSlot = $examSlot;
        $this->subject = $subject;
    }

    /**
     * Get all exam records for all studentsin a class section for a semester.
     *
     * @param int $section
     * @param int $subject
     *
     * @return App\Modles\ExamRecord
     */
    public function getAllExamRecordsInSectionAndSubject(int $section, int $subject)
    {
        return ExamRecord::where(['section_id' => $section, 'subject_id' => $subject])->get();
    }

    /**
     * Get all exam records in section.
     *
     * @param int $section
     *
     * @return App\Models\ExamRecord
     */
    public function getAllExamRecordsInSection(int $section)
    {
        return ExamRecord::where('section_id', $section)->get();
    }

    /**
     * Get all exam records for a subject.
     *
     * @param Exam $exam
     * @param int  $user
     * @param int  $subject
     *
     * @return App\Models\ExamRecord
     */
    public function getAllUserExamRecordInExamForSubject(Exam $exam, int $user, int $subject)
    {
        //get all exam slots in exam
        $examSlots = $exam->examSlots->pluck('id');
        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user, 'subject_id' => $subject])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Get all exam records for a user in a subject and a specofoc semester.
     *
     * @param Semester $semester
     * @param int      $user
     * @param int      $subject
     *
     * @return App\Models\ExamRecord
     */
    public function getAllUserExamRecordInSemesterForSubject(Semester $semester, $user, $subject)
    {
        //get all exams
        $exams = $semester->exams;
        //create container variable for all exam slots in semester
        $examSlots = [];
        //get all exam slots in exams
        foreach ($exams as $exam) {
            $i = $exam->examSlots->pluck('id')->toArray();
            foreach ($i as $j) {
                $examSlots[] = $j;
            }
        }
        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user, 'subject_id' => $subject])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Get all user exam records for user in a semester.
     *
     * @param Semester $semester
     * @param int      $user
     *
     * @return App\Models\ExamRecord
     */
    public function getAllUserExamRecordInSemester(Semester $semester, int $user)
    {
        //get all exams
        $exams = $semester->exams;
        //create container variable for all exam slots in semster
        $examSlots = [];
        //get all exam slots in exams
        foreach ($exams as $exam) {
            $i = $exam->examSlots->pluck('id')->toArray();
            foreach ($i as $j) {
                $examSlots[] = $j;
            }
        }
        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Create exam record.
     *
     * @param array|object $records
     *
     * @return void
     */
    public function createExamRecord($records)
    {
        if (auth()->user()->hasRole('teacher') && $this->subject->getSubjectById($records['subject_id'])->teachers->where('id', auth()->user()->id)->isEmpty()) {
            return session()->flash('danger', 'You are not authorized to create exam record for this subject');
        }
        //started transaction to make sure everything ran smoothly before saving
        DB::beginTransaction();

        foreach ($records['exam_records'] as $record) {
            // makes sure student marks and exam slot id are not null just an extra check ad=s this is already done in request class
            if ($record['student_marks'] == null || $this->examSlot->getExamSlotById($record['exam_slot_id'])->total_marks == null) {
                //stop db transaction and return error
                DB::rollback();
                session()->flash('danger', 'Incomplete records submitted');

                return;
            }

            // checks if student marks is less than total marks
            if ($record['student_marks'] > $this->examSlot->getExamSlotById($record['exam_slot_id'])->total_marks) {
                //stop db transaction and return error
                DB::rollback();
                session()->flash('danger', 'Student marks cannot be greater than total marks');

                return;
            }

            // creates exam record or updates if records already exists

            ExamRecord::updateOrCreate(
                ['user_id'         => $records['user_id'],
                    'section_id'   => $records['section_id'],
                    'subject_id'   => $records['subject_id'],
                    'exam_slot_id' => $record['exam_slot_id'],
                ],
                [
                    'student_marks' => $record['student_marks'],
                ]
            );
        }

        DB::commit();
        session()->flash('success', 'Exam Records Created Successfully');
    }
}
