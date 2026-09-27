<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\Database\Eloquent\Collection;

class ExamSlotService
{
    /**
     * Get all exam slots in exam.
     *
     *
     * @return Collection<int, ExamSlot>
     */
    public function getAllExamSlots(Exam $exam)
    {
        return $exam->examSlots;
    }

    /**
     * Get an exam slot by id.
     *
     * @param  int  $id
     * @return ExamSlot|null
     */
    public function getExamSlotById($id)
    {
        return ExamSlot::find($id);
    }

    /**
     * Delete exam slot.
     *
     *
     * @return void
     */
    public function deleteExamSlot(ExamSlot $examSlot)
    {
        $examSlot->delete();
    }
}
