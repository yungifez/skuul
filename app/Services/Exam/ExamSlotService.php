<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\Support\Facades\DB;

class ExamSlotService
{
    //get all exam slots for exam

    public function getAllExamSlots(Exam $exam)
    {
        return $exam->examSlots;
    }

    //get examslot by id

    public function getExamSlotById($id)
    {
        return ExamSlot::find($id);
    }

    //create exam slot

    public function createExamSlot(Exam $exam, array $data)
    {
        DB::transaction(function () use ($data, $exam) {
            if (!isset($data['description'])) {
                $data['description'] = null;
            }
            $exam->examSlots()->create([
                'name'        => $data['name'],
                'description' => $data['description'],
                'total_marks' => $data['total_marks'],
            ]);
        });

        return session()->flash('success', 'Exam Slot Created Successfully');
    }

    //update exam slot

    public function updateExamSlot(ExamSlot $examSlot, array $data)
    {
        DB::transaction(function () use ($data, $examSlot) {
            if (!isset($data['description'])) {
                $data['description'] = null;
            }
            $examSlot->update([
                'name'        => $data['name'],
                'description' => $data['description'],
                'total_marks' => $data['total_marks'],
            ]);
        });

        return session()->flash('success', 'Exam Slot Updated Successfully');
    }

    //delete exam slot

    public function deleteExamSlot(ExamSlot $examSlot)
    {
        $examSlot->delete();

        return session()->flash('success', 'Exam Slot Deleted Successfully');
    }
}
