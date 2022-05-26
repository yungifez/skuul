<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Section;
use Livewire\Component;
use App\Services\Exam\ExamService;
use Illuminate\Support\Facades\Cache;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;

class ExamTabulation extends Component
{
    public $exam, $class, $section, $exams, $classes, $sections, $semester, $subjects, $students, $totalMarksAttainableInEachSubject, $tabulatedRecords, $grades;

    public function mount(ExamService $examService, SectionService $sectionService, MyClassService $myClassService)
    {
        //get semester and use it to fetch all exams in semester
        $this->semester = auth()->user()->school->semester;
        $this->exams = $examService->getAllExamsInSemester($this->semester->id);
        //set exam as first exam if exams not empty
        $this->exams->count() ? $this->exam = $this->exams[0]->id : $this->exam = null;
        $this->classes = $myClassService->getAllClasses();
        //sets subjects etc if class isnt empty
        if (!$this->classes->isEmpty()) {
            $this->sections = $this->classes[0]->sections;
            $this->section = $this->sections[0]->id;
        }
    }

    public function updatedClass()
    {
        //get instance of class
        $class = app("App\Services\MyClass\MyClassService")->getClassById($this->class);
        //get sections in class
        $this->sections = $class->sections;
        //set section if the fetched records aren't empty
        $this->sections->count() ? $this->section = $this->sections[0]->id : $this->section = null;
    }

    public function tabulate(Exam $exam, Section $section)
    {
        //get total marks attainable in each subject
        $this->totalMarksAttainableInEachSubject = app('App\Services\Exam\ExamService')-> totalMarksAttainableInEachSubject($exam);

        //get all subjects in section
        $this->subjects = $section->myClass->subjects;

        //get all students in section
        $this->students = $section->studentRecords->map(function ($studentRecord) {
            return $studentRecord->user;
        });

        //get all exam records in section
        $this->examRecords = app('App\Services\Exam\ExamRecordService')->getAllExamRecordsInSection($section->id);
        $this->tabulatedRecords = Cache::get("exam-tabulation-".$exam->id."-".$section->id, function () use ($exam, $section) {
            return $this->createTabulation($exam, $section);
        });
    }

    //tabulates the result
    public function createTabulation(Exam $exam, Section $section)
    {
        //create tabulation 
        $tabulatedRecords = [];
        foreach ($this->students as $student ) {
            //array to hold tabulation values for each student
            $totalSubjectMarks = [];
            //set student name and admission number
            $tabulatedRecords[$student->id]['student_name'] = $student->name;
            $tabulatedRecords[$student->id]['admission_number'] = $student->studentRecord->admission_number;
            //loop through all subjects and add all marks
            foreach ($this->subjects->sortBy('name') as $subject ) {
                $tabulatedRecords[$student->id]['student_marks'][$subject->id] = app('App\Services\Exam\ExamService')->calculateStudentTotalMarksInSubject($student, $subject);
                //array used for calculating total marks
                $totalSubjectMarks[] = $tabulatedRecords[$student->id]['student_marks'][$subject->id];
            }
            //turned to object
            $totalSubjectMarks = collect($totalSubjectMarks)->sum();
            //set total from summing each subject
            $tabulatedRecords[$student->id]['total'] = $totalSubjectMarks;
            //calculated percentage
            $tabulatedRecords[$student->id]['percent'] =round( ($totalSubjectMarks / ($this->totalMarksAttainableInEachSubject * $this->subjects->count())) * 100, 2);
            $percentage = $tabulatedRecords[$student->id]['percent'];

            $grade = app('App\Services\GradeSystem\GradeSystemService')->getGrade($section->myClass->classGroup->id, $percentage);
            //get appropriate grade
            $tabulatedRecords[$student->id]['grade'] =  $grade ? $grade->name : 'No Grade';
        }
        //creates cache for tabulation
        Cache::put("exam-tabulation-".$exam->id."-".$section->id, $this->tabulatedRecords, 3600);
        
        return collect($tabulatedRecords);
    }

    public function render()
    {
        return view('livewire.exam-tabulation');
    }
}
