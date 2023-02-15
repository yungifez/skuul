<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\Support\Facades\DB;

class ExamSlotService
{
    /**
     * Get all exam slots in exam.
     *
     *
     * @return Illumiate\Database\Eloquent\Collection|static[]
     */
    public function getAllExamSlots(Exam $exam)
    {
        return $exam->examSlots;
    }

    /**
     * Get an exam slot by id.
     *
     * @param int $id
     *
     * @return App\Models\ExamSlot
     */
    public function getExamSlotById($id)
    {
        return ExamSlot::find($id);
    }

    /**
     * Create exam slot.
     *
     *
     * @return void
     */
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
    }

    /**
     * Update exam slot.
     *
     *
     * @return void
     */
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
