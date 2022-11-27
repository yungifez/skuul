<?php

namespace App\Http\Livewire;

use App\Models\Section;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use App\Traits\MarkTabulationTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class ResultTabulation extends Component
{
    use MarkTabulationTrait;

    public $section;
    public $sections;
    public $classes;
    public $class;
    public $semester;
    public $tabulatedRecords;
    public $createdTabulation;

    protected $listeners = ['print'];

    public function mount(SectionService $sectionService, MyClassService $myClassService)
    {
        //get semester and use it to fetch all exams in semester
        $this->semester = auth()->user()->school->semester;
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

    public function tabulate(Section $section)
    {
        //get all subjects in section
        $subjects = $section->myClass->subjects;

        //get all students in section
        $students = $section->studentRecords->load('user')->map(function ($studentRecord) {
            return $studentRecord->user;
        });

        if ($students->isEmpty()) {
            $this->createdTabulation = false;

            return $tabulatedRecords;
        }

        $examSlots = $this->semester->load('examSlots')->examSlots;

        $this->tabulatedRecords = $this->tabulateMarks($section->myClass->classGroup, $subjects, $students, $examSlots);

        $this->createdTabulation = true;
    }

    //print function

    public function print()
    {
        //used pdf class directly
        $pdf = Pdf::loadView('pages.exam.print-exam-tabulation', ['tabulatedRecords' => $this->tabulatedRecords, 'totalMarksAttainableInEachSubject' => $this->totalMarksAttainableInEachSubject, 'subjects' => $this->subjects])->output();
        //save as pdf
        return response()->streamDownload(
            fn () => print($pdf),
            'result-tabiulation.pdf'
        );
    }

    public function render()
    {
        return view('livewire.result-tabulation');
    }
}
