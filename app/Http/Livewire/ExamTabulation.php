<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Section;
use Livewire\Component;
use App\Models\ExamRecord;
use App\Models\GradeSystem;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Exam\ExamService;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;

class ExamTabulation extends Component
{
    public $exam;
    public $class;
    public $section;
    public $exams;
    public $classes;
    public $sections;
    public $semester;
    public $subjects;
    public $students;
    public $totalMarksAttainableInEachSubject;
    public $tabulatedRecords;
    public $grades;

    protected $listeners = ['print'];

    public function mount(ExamService $examService, SectionService $sectionService, MyClassService $myClassService)
    {

        //get semester and use it to fetch all exams in semester
        $this->semester = auth()->user()->school->semester;
        $this->exams = $examService->getAllExamsInSemester($this->semester->id);

        //set exam as first exam if exams not empty
        $this->exams->count() ? $this->exam = $this->exams[0]->id : $this->exam = null;
        $this->classes = $myClassService->getAllClasses();

        //sets subjects etc if class isn't empty
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
        $this->totalMarksAttainableInEachSubject = app('App\Services\Exam\ExamService')->totalMarksAttainableInExamForSubject($exam);

        //get all subjects in section
        $this->subjects = $section->myClass->subjects;

        //get all students in section
        $this->students = $section->studentRecords()->with('user')->get()->map(function ($studentRecord) {
            return $studentRecord->user;
        });

        //get tabulation from cache else create new one
        $this->tabulatedRecords = $this->createTabulation($exam, $section);
    }

    //tabulates the result
    private function createTabulation(Exam $exam, Section $section)
    {
        //create tabulation
        $tabulatedRecords = [];

        //get all relevant exam records
        $examRecords = ExamRecord::whereIn('subject_id', $this->subjects->pluck('id'))->whereIn('user_id', $this->students->pluck('id'))->get();
        //get all exam slots 
        $examSlots = $exam->load('examSlots')->examSlots;
        //get all grades in class group
        $grades =  GradeSystem::where( 'class_group_id', $section->myClass->classGroup->id )->get();

        foreach ($this->students->load('studentRecord') as $student) {

            //array to hold tabulation values for each student
            $totalSubjectMarks = [];

            //set student name and admission number
            $tabulatedRecords[$student->id]['student_name'] = $student->name;
            $tabulatedRecords[$student->id]['admission_number'] = $student->studentRecord->admission_number;

            //loop through all subjects and add all marks
            foreach ($this->subjects as $subject) {
                $tabulatedRecords[$student->id]['student_marks'][$subject->id] = $examRecords->where('user_id' , $student->id)->whereIn('exam_slot_id', $examSlots->pluck('id'))->where('subject_id' , $subject->id)->pluck('student_marks')->sum();

                //array used for calculating total marks
                $totalSubjectMarks[] = $tabulatedRecords[$student->id]['student_marks'][$subject->id];
            }

            //turned to object
            $totalSubjectMarks = collect($totalSubjectMarks)->sum();

            //set total from summing each subject
            $tabulatedRecords[$student->id]['total'] = $totalSubjectMarks;

            //calculated percentage
            $totalMarks = $this->totalMarksAttainableInEachSubject * $this->subjects->count();

            //make sure total marks is not 0
            $totalMarks = $totalMarks ? $totalMarks : 1;
            $tabulatedRecords[$student->id]['percent'] = ceil((($totalSubjectMarks / $totalMarks)) * 100);
            $percentage = $tabulatedRecords[$student->id]['percent'];
            $grade = $grades->where('grade_from', '<=', $percentage)->where('grade_till', '>=', $percentage)->first();
            
            //get appropriate grade
            $tabulatedRecords[$student->id]['grade'] = $grade ? $grade->name : 'No Grade';
        }

        return collect($tabulatedRecords);
    }

    //print function

    public function print()
    {
        //used pdf class directly
        $pdf = Pdf::loadView('pages.exam.print-exam-tabulation', ['tabulatedRecords' => $this->tabulatedRecords, 'totalMarksAttainableInEachSubject' => $this->totalMarksAttainableInEachSubject, 'subjects' => $this->subjects])->output();
        //save as pdf
        return response()->streamDownload(
            fn () => print($pdf),
            'exam-tabiulation.pdf'
        );
    }

    public function render()
    {
        return view('livewire.exam-tabulation');
    }
}
