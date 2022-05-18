<?php
namespace App\Services\Exam;

use App\Models\Exam;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function getAllExamsInSemester($semester_id)
    {
        return Exam::where('semester_id', $semester_id)->get();
    }

    //get exam by id

    public function getExamById($id)
    {
        return Exam::find($id);
    }
    
    public function createExam($records)
    {
        DB::transaction(function () use ($records)
        {
            $exam = Exam::create([
                'name' => $records['name'],
                'description' => $records['description'],
                'semester_id' => $records['semester_id'],
                'start_date' => $records['start_date'],
                'stop_date' => $records['stop_date'],
            ]);
        });
        
        return session()->flash('success', 'Exam created successfully');
    }

    public function updateExam(Exam $exam, $records)
    {
        $exam->name = $records['name'];
        $exam->description = $records['description'];
        $exam->semester_id = $records['semester_id'];
        $exam->start_date = $records['start_date'];
        $exam->stop_date = $records['stop_date'];
        $exam->save();

        return session()->flash('success', 'Exam updated successfully');
    }

    public function deleteExam(Exam $exam)
    {
        $exam->delete();

        return session()->flash('success', 'Exam deleted successfully');
    }
}