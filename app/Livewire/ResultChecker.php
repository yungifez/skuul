<?php

namespace App\Livewire;

use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\MyClass\MyClassService;
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

    public $academicPeriods;

    public $academicPeriod;

    public $exams;

    public $examRecords;

    public $subjects;

    public $preparedResults;

    public $status;

    public $studentName;

    public $selectedClass;

    // rules
    public $rules = [
        'academicYear' => 'integer|exists:academic_years,id',
        'academicPeriod' => 'required|integer|exists:academic_periods,id',
    ];

    public function mount(MyClassService $myClassService)
    {
        $this->academicYears = current_school()->academicYears;
        $this->academicYear = current_academic_year()->id;
        $this->updatedAcademicYear();
        if (auth()->user()->can(PlatformPermission::AccessAllSchools) || auth()->user()->hasAnyRole([Role::Admin, Role::Teacher])) {
            $this->classes = $myClassService->getAllClasses();

            if ($this->classes->isEmpty()) {
                return;
            }
            $this->class = $this->classes[0]->id;
            $this->updatedClass();
        } elseif (auth()->user()->hasRole(Role::Student)) {
            $this->checkResult(current_academic_year(), current_academic_period(), auth()->user()->loadMissing('allStudentRecords'));
        } elseif (auth()->user()->hasRole(Role::Parent)) {
            // get parent's children
            $this->students = auth()->user()->parentRecord->Students;
            // set student if the fetched records aren't empty
            $this->students->count() ? $this->student = $this->students[0]->id : $this->student = null;
        }
    }

    // updated academic year
    public function updatedAcademicYear()
    {
        $academicYear = app("App\Services\AcademicYear\AcademicYearService")->getAcademicYearById($this->academicYear);
        // get academic periods in academic year
        $this->academicPeriods = $academicYear->academicPeriods;
        $this->academicPeriod = null;

        if ($this->academicPeriods->isEmpty()) {
            return;
        }

        $this->academicPeriod = ($this->academicPeriods->find(current_academic_period_id()) ?? $this->academicPeriods[0])->id;
    }

    public function updatedClass()
    {
        // get instance of class
        $class = app("App\Services\MyClass\MyClassService")->getClassById($this->class);

        // get sections in class
        $this->sections = $class->sections;

        // set section if the fetched records aren't empty
        if ($this->sections->isEmpty()) {
            $this->students = null;

            return;
        }
        $this->section = $this->sections[0]->id;

        $this->updatedSection();
    }

    public function updatedSection()
    {
        // get instance of section
        $section = app("App\Services\Section\SectionService")->getSectionById($this->section);

        // get students in section
        $this->students = $section->students();

        // set student if the fetched records aren't empty
        $this->students->count() ? $this->student = $this->students[0]->id : $this->student = null;
    }

    public function checkResult(AcademicYear $academicYear, $academicPeriod, User $student)
    {
        $academicPeriod = $this->academicPeriods->find($academicPeriod);

        // make sure user student isn't another role
        if (!$student->hasRole(Role::Student)) {
            abort(404, 'Student not found.');
        }
        // set name that would be used in view
        $this->studentName = $student->name;
        // fetch all exams, subjects and exam records for user in academic period

        if ($academicPeriod != null && $academicPeriod->exists()) {
            $this->exams = $academicPeriod->exams()->where('publish_result', true)->get()->load('examSlots');
            // fetch all students exam records in academic period
            $this->examRecords = app("App\Services\Exam\ExamRecordService")->getAllUserExamRecordInAcademicPeriod($academicPeriod, $student->id);
        } else {
            $this->exams = $academicYear->exams()->where('publish_result', true)->get()->load('examSlots');
            $this->examRecords = app("App\Services\Exam\ExamRecordService")->getAllUserExamRecordInAcademicYear($academicYear, $student->id);
        }

        if ($this->exams->isEmpty()) {
            $this->status = 'There are no exams with published results for now';

            return $this->preparedResults = false;
        }

        $academicYearsWithStudentRecords = $student->allStudentRecords->academicYears()->where('academic_year_id', $this->academicYear)->first();
        if (is_null($academicYearsWithStudentRecords)) {
            $this->status = 'No records this academic year, make sure user has been promoted this year or has not been graduated';
            $this->preparedResults = false;

            return;
        }
        $this->subjects = $academicYearsWithStudentRecords->studentAcademicYearBasedRecords->class->subjects;

        if ($this->subjects->isEmpty()) {
            $this->status = 'Subjects not present';
            $this->preparedResults = false;

            return;
        }

        $this->preparedResults = true;
        $this->selectedClass = $academicYearsWithStudentRecords->studentAcademicYearBasedRecords->class;
    }

    public function render()
    {
        return view('livewire.result-checker');
    }
}
