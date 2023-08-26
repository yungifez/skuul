<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Services\Exam\ExamService;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use App\Services\Subject\SubjectService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class ListExamRecordsTable extends Component
{
    use WithPagination;

    protected $queryString = ['sectionSelectedId', 'examSelectedId', 'subjectSelectedId', 'search'];

    public $semester;

    public $search;

    public Collection $exams;

    public $examSlots;

    public $exam;

    public $classes;

    public $class;

    public $subjects;

    public $subject;

    public $sections;

    public $section;

    public $examRecords;

    public $classSelected;

    public $subjectSelected;

    public $sectionSelected;

    public $examSelected;

    public $error;

    public $sectionSelectedId;

    public $examSelectedId;

    public $subjectSelectedId;

    public function mount(ExamService $examService, MyClassService $myClassService, SectionService $sectionService, SubjectService $subjectService)
    {
        //get semester and use it to fetch all exams in semester
        $this->semester = auth()->user()->school->semester;
        $this->exams = $examService->getActiveExamsInSemester($this->semester->id);
        //set exam as first exam if exams not empty
        $this->exams->count() ? $this->exam = $this->exams[0]->id : $this->exam = null;
        $this->classes = $myClassService->getAllClasses();
        //sets subjects etc if class isn't empty
        if (!$this->classes->isEmpty()) {
            $this->subjects = $this->classes[0]->subjects;
            if ($this->subjects->isNotEmpty()) {
                $this->subject = $this->subjects[0]->id;
            }
            $this->sections = $this->classes[0]->sections;
            if ($this->sections->isNotEmpty()) {
                $this->section = $this->sections[0]->id;
            }
        }

        //if url contains query strings pass them to fetch student to preserve state
        if (isset($this->sectionSelectedId) && isset($this->examSelectedId) && isset($this->subjectSelectedId)) {
            $exam = $examService->getExamById($this->examSelectedId);
            $section = $sectionService->getSectionById($this->sectionSelectedId);
            $subject = $subjectService->getSubjectById($this->subjectSelectedId);
            $this->fetchExamRecords($exam, $section, $subject);
        }
    }

    public function updatedClass()
    {
        //get instance of class
        $class = app("App\Services\MyClass\MyClassService")->getClassById($this->class);
        //get subjects in class
        $this->subjects = $class->subjects;
        //get sections in class
        $this->sections = $class->sections;
        //set subject and section if the fetched records aren't empty
        $this->subjects->count() ? $this->subject = $this->subjects[0]->id : $this->subject = null;
        $this->sections->count() ? $this->section = $this->sections[0]->id : $this->section = null;
    }

    public function fetchExamRecords(Exam $exam, Section $section, Subject $subject)
    {
        $this->search = null;
        $this->examSlots = $exam->examSlots;
        $this->examRecords = ExamRecord::inSubject($subject->id)->inSection($section->id)->get();
        if ($this->examSlots->isEmpty()) {
            $this->examSlots = null;
            $this->error = 'No exam slots found';

            return;
        }

        //set variables used for controlling state, holding state data and querystrings
        $this->examSelected = $exam;
        $this->examSelectedId = $this->examSelected->id;
        $this->sectionSelected = $section;
        $this->sectionSelectedId = $this->sectionSelected->id;
        $this->classSelected = $section->myClass;
        $this->subjectSelected = $subject;
        $this->subjectSelectedId = $this->subjectSelected->id;
    }

    public function render()
    {
        $section = $this->sectionSelected;
        if ($section != null && $section->exists()) {
            $students = User::students()->inSchool()->whereRelation('studentRecord.section', 'id', $section->id)->where('name', 'LIKE', "%$this->search%")->orderBy('name')->paginate(10);
            $viewData = ['students' => $students];
        } else {
            $viewData = [];
        }

        return view('livewire.list-exam-records-table', $viewData);
    }
}
