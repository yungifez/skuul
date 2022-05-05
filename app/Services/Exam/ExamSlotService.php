<?php

namespace App\Services\Exam;

use App\Models\Exam;
use Illuminate\Support\Facades\DB;

class ExamSlotService{
    
    //get all exam slots for exam

    public function getAllExamSlots(Exam $exam){
        return $exam->examSlots;
    }

    //create exam slot

    public function createExamSlot(Exam $exam, array $data){

        DB::transaction(function () use ($data,$exam) {
            if (!isset($data['description'])) {
                $data['description'] = null;
            }
            $exam->examSlots()->create([
                'name' => $data['name'],
                'description' => $data['description'],
                'total_marks' => $data['total_marks'],
            ]);
        });
        
    }

    //update exam slot


}