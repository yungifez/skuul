<?php

namespace App\Http\Livewire;

use App\Models\Section;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ResultTabulation extends Component
{
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
        //get total marks attainable in each subject
        $this->totalMarksAttainableInEachSubject = app('App\Services\Exam\ExamService')->totalMarksAttainableInSemesterForSubject($this->semester);

        //get all subjects in section
        $this->subjects = $section->myClass->subjects;

        //get all students in section
        $this->students = $section->studentRecords->map(function ($studentRecord) {
            return $studentRecord->user;
        });

        $this->tabulatedRecords = Cache::get('result-tabulation-'.$section->id, function () use ($section) {
            return $this->createTabulation($section);
        });
    }

    //tabulates the
    private function createTabulation(Section $section)
    {
        //create tabulation
        $tabulatedRecords = [];
        if ($this->students->isEmpty()) {
            $this->createdTabulation = false;

            return $tabulatedRecords;
        }
        foreach ($this->students as $student) {
            //array to hold tabulation values for each student
            $totalSubjectMarks = [];

            //set student name and admission number
            $tabulatedRecords[$student->id]['student_name'] = $student->name;
            $tabulatedRecords[$student->id]['admission_number'] = $student->studentRecord->admission_number;

            //loop through all subjects and add all marks
            foreach ($this->subjects->sortBy('name') as $subject) {
                $tabulatedRecords[$student->id]['student_marks'][$subject->id] = app('App\Services\Exam\ExamService')->calculateStudentTotalMarkInSubjectForSemester($this->semester, $student, $subject);

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

            $grade = app('App\Services\GradeSystem\GradeSystemService')->getGrade($section->myClass->classGroup->id, $percentage);

            //get appropriate grade
            $tabulatedRecords[$student->id]['grade'] = $grade ? $grade->name : 'No Grade';
        }

        //creates cache for tabulation
        Cache::put('exam-tabulation-'.$section->id, $this->tabulatedRecords, 3600);
        $this->createdTabulation = true;

        return collect($tabulatedRecords);
    }

    //print function

    public function print()
    {
        //used pdf class directly, I used exam tabulation view since it contains same logic
        $pdf = Pdf::loadView('pages.exam.print-exam-tabulation', ['tabulatedRecords' => $this->tabulatedRecords, 'totalMarksAttainableInEachSubject' => $this->totalMarksAttainableInEachSubject, 'subjects' => $this->subjects]);
        $randomString = str()->random();
        //save as pdf
        $pdf->save("temp-pdf/result-tabulation$randomString.pdf");
        //download
        return response()->download("temp-pdf/result-tabulation$randomString.pdf", 'result-tabulation.pdf');
    }

    public function render()
    {
        return view('livewire.result-tabulation');
    }
}
