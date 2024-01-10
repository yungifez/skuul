<?php

namespace App\Services\Exam;

use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\Semester;
use App\Services\Subject\SubjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ExamRecordService
{
    protected ExamSlotService $examSlotService;

    /**
     * Subject service class.
     */
    protected SubjectService $subjectService;

    public function __construct(ExamSlotService $examSlotService, SubjectService $subjectService)
    {
        $this->examSlotService = $examSlotService;
        $this->subjectService = $subjectService;
    }

    /**
     * Get all exam records for all students in a class section for a semester.
     *
     *
     * @return \App\Modles\ExamRecord
     */
    public function getAllExamRecordsInSectionAndSubject(int $section, int $subject)
    {
        return ExamRecord::where(['section_id' => $section, 'subject_id' => $subject])->get();
    }

    /**
     * Get all exam records in section.
     *
     *
     * @return \App\Models\ExamRecord
     */
    public function getAllExamRecordsInSection(int $section)
    {
        return ExamRecord::where('section_id', $section)->get();
    }

    /**
     * Get all exam records for a subject.
     *
     *
     * @return \App\Models\ExamRecord
     */
    public function getAllUserExamRecordInExamForSubject(Exam $exam, int $user, int $subject)
    {
        //get all exam slots in exam
        $examSlots = $exam->examSlots->pluck('id');

        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user, 'subject_id' => $subject])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Get all exam records for a user in a subject and an específic semester.
     *
     * @param int $user
     * @param int $subject
     *
     * @return \App\Models\ExamRecord
     */
    public function getAllUserExamRecordInSemesterForSubject(Semester $semester, $user, $subject)
    {
        //get all exams
        $exams = $semester->exams;
        //create container variable for all exam slots in semester
        $examSlots = [];
        //get all exam slots in exams
        foreach ($exams as $exam) {
            $i = $exam->examSlots->pluck('id')->all();
            foreach ($i as $j) {
                $examSlots[] = $j;
            }
        }

        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user, 'subject_id' => $subject])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Get all user exam records for user in an academic year.
     *
     * @param Semester $semester
     *
     * @return \App\Models\ExamRecord
     */
    public function getAllUserExamRecordInAcademicYear(AcademicYear $academicYear, int $user)
    {
        //get all exams
        $exams = $academicYear->exams->load('examSlots');
        $examSlots = $this->getAllExamSlotsInExams($exams);

        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Get all user exam records for user in a semester.
     *
     *
     * @return \App\Models\ExamRecord
     */
    public function getAllUserExamRecordInSemester(Semester $semester, int $user)
    {
        //get all exams
        $exams = $semester->exams->load('examSlots');

        $examSlots = $this->getAllExamSlotsInExams($exams);

        //get all exam records in for user and subject
        return ExamRecord::where(['user_id' => $user])->whereIn('exam_slot_id', $examSlots)->get();
    }

    /**
     * Get all exam slots in exam.
     *
     *
     * @return void
     */
    public function getAllExamSlotsInExams($exams)
    {
        //create container variable for all exam slots in semester
        $examSlots = [];
        //get all exam slots in exams
        foreach ($exams as $exam) {
            $i = $exam->examSlots->pluck('id')->all();
            foreach ($i as $j) {
                $examSlots[] = $j;
            }
        }

        return $examSlots;
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
        if (auth()->user()->hasRole('teacher') && $this->subjectService->getSubjectById($records['subject_id'])->teachers->where('id', auth()->user()->id)->isEmpty()) {
            throw new AuthorizationException('Creating of exam record for this subject is unauthorised.');
        }

        DB::transaction(function () use ($records) {
            foreach ($records['exam_records'] as $record) {
                //set mark if not present
                $record['student_marks'] = $record['student_marks'] ?? null;

                // checks if student marks is less than total marks
                if ($record['student_marks'] > $this->examSlotService->getExamSlotById($record['exam_slot_id'])->total_marks) {
                    throw new InvalidValueException('Student marks cannot be greater than total marks', 1);
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
        });
    }
}
