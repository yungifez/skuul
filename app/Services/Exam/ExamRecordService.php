<?php

namespace App\Services\Exam;

use App\Models\ExamRecord;
use Illuminate\Support\Facades\DB;
use App\Services\Exam\ExamSlotService;

class ExamRecordService{

    protected ExamSlotService $examSlot;

    public function __construct(ExamSlotService $examSlot)
    {
        $this->examSlot = $examSlot;
    }

    public function getAllExamRecordsInSectionAndSubject($section, $subject)
    {
        return ExamRecord::where(['section_id' => $section, 'subject_id' => $subject])->get();
    }

    public function createExamRecord($records)
    {
        foreach ($records['exam_records'] as $record) {
            // makes sure student marks and exam slot id are not null just an extra check ad=s this is already doine in request class
            if ($record['student_marks'] == null || $this->examSlot->getExamSlotById($record['exam_slot_id'])->total_marks == null) {
                return session()->flash('danger', 'Incomplete records submitted');
            }

            // checks if student marks is less than total marks
            if ($record['student_marks'] > $this->examSlot->getExamSlotById($record['exam_slot_id'])->total_marks) {
                return session()->flash('danger', 'Student marks cannot be greater than total marks');
            }

            // creates exam record or updates if records already exists
            DB::transaction(function () use ($record,$records) {
                ExamRecord::updateOrCreate(
                    ['user_id' => $records['user_id'],
                    'section_id' => $records['section_id'],
                    'subject_id' => $records['subject_id'],
                    'exam_slot_id' => $record['exam_slot_id'],
                ],
                [
                    'student_marks' => $record['student_marks'],
                ]);
            });
        }

        return session()->flash('success', 'Exam Records Created Successfully');
    }
}