<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Section;
use App\Services\Exam\ExamService;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use App\Traits\MarkTabulationTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class ExamTabulation extends Component
{
    use MarkTabulationTrait;
    public $exam;
    public $class;
    public $section;
    public $exams;
    public $classes;
    public $sections;
    public $semester;
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
        //get all subjects in section
        $subjects = $section->myClass->subjects;

        //get all students in section
        $students = $section->studentRecords()->with('user')->get()->map(function ($studentRecord) {
            return $studentRecord->user;
        });
        //get all exam slots
        $examSlots = $exam->load('examSlots')->examSlots;

        $this->tabulatedRecords = $this->tabulateMarks($section->myClass->classGroup, $subjects, $students, $examSlots);
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
