<?php

namespace App\Http\Livewire;

use App\Models\Semester;
use App\Models\User;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use Livewire\Component;

class ResultChecker extends Component
{
    public $section;
    public $sections;
    public $classes;
    public $class;
    public $students;
    public $student;
    public $academicYears;
    public $academicYear;
    public $semesters;
    public $semester;
    public $exams;
    public $examRecords;
    public $subjects;
    public $preparedResults;
    public $status;
    public $studentName;

    //rules
    public $rules = [
        'academicYear' => 'integer|exists:academic_years,id',
        'semester'     => 'required',
    ];

    public function mount(SectionService $sectionService, MyClassService $myClassService)
    {
        $this->academicYears = auth()->user()->school->academicYears;
        $this->academicYear = $this->academicYears->first()->id;
        $this->updatedAcademicYear();
        if (auth()->user()->hasAnyRole(['super-admin', 'admin', 'teacher'])) {
            $this->classes = $myClassService->getAllClasses();

            if ($this->classes->isEmpty()) {
                return;
            }
            $this->class = $this->classes[0]->id;
            $this->updatedClass();
        } elseif (auth()->user()->hasRole('student')) {
            $this->checkResult(auth()->user()->school->semester, auth()->user());
        } elseif (auth()->user()->hasRole('parent')) {
            //get parent's children
            $this->students = auth()->user()->parentRecord->Students;
            //set student if the fetched records aren't empty
            $this->students->count() ? $this->student = $this->students[0]->id : $this->student = null;
        }
    }

    //updated academic year
    public function updatedAcademicYear()
    {
        $academicYear = app("App\Services\AcademicYear\AcademicYearService")->getAcademicYearById($this->academicYear);
        //get semesters in academic year
        $this->semesters = $academicYear->semesters;
        $this->semester = null;

        if ($this->semesters->isEmpty()) {
            return;
        }

        $this->semester = $this->semesters[0]->id;
    }

    public function updatedClass()
    {
        //get instance of class
        $class = app("App\Services\MyClass\MyClassService")->getClassById($this->class);

        //get sections in class
        $this->sections = $class->sections;

        //set section if the fetched records aren't empty
        if ($this->sections->isEmpty()) {
            $this->students = null;

            return;
        }
        $this->section = $this->sections[0]->id;

        $this->updatedSection();
    }

    public function updatedSection()
    {
        //get instance of section
        $section = app("App\Services\Section\SectionService")->getSectionById($this->section);

        //get students in section
        $this->students = $section->studentRecords->map(function ($studentRecord) {
            return $studentRecord->user;
        });

        //set student if the fetched records aren't empty
        $this->students->count() ? $this->student = $this->students[0]->id : $this->student = null;
    }

    public function checkResult(Semester $semester, User $student)
    {
        // make sure user student isn't another role
        if (!$student->hasRole('student')) {
            abort(404, 'Student not found.');
        }
        //set name that would be used in view
        $this->studentName = $student->name;
        // fetch all exams, subjects and exam records for user in semester
        $this->exams = $semester->exams()->where('publish_result', true)->get();
        if ($this->exams->isEmpty()) {
            $this->status = 'There are no exams with published results for now';

            return $this->preparedResults = false;
        }

        $this->subjects = $student->studentRecord->myClass->subjects;

        //fetch all students exam records in semester
        $this->examRecords = app("App\Services\Exam\ExamRecordService")->getAllUserExamRecordInSemester($semester, $student->id);

        $this->preparedResults = true;
    }

    public function render()
    {
        return view('livewire.result-checker');
    }
}
