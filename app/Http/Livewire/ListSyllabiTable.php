<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ListSyllabiTable extends Component
{
    use WithPagination;
    public $class;
    public $syllabi;
    public $classes;
    protected $queryString = ['page'];
    protected $paginationTheme = 'bootstrap';

    public function mount(SyllabusService $syllabusService, MyClassService $myClassService)
    {
        $semester = auth()->user()->school->semester_id;
        if (auth()->user()->hasRole('student')) {
            $this->class = auth()->user()->studentRecord->myClass->name;
            $class = auth()->user()->studentRecord->my_class_id;
            $this->syllabi = $syllabusService->getAllSyllabiInSemesterAndClass($semester, $class)->load('subject');
        } else {
            $this->classes = $myClassService->getAllClasses();
            //make sure classes arent empty
            if (!$this->classes->isEmpty()) {
                $this->syllabi = $syllabusService->getAllSyllabiInSemesterAndClass($semester, $this->classes[0]['id'])->load('subject');
            } else {
                $this->classes = [];
                $this->syllabi = collect([]);
            }

            if ($this->syllabi->isEmpty()) {
                $this->syllabi = null;
            }
        }
    }

    public function updatedClass()
    {
        $semester = auth()->user()->school->semester_id;
        $this->syllabi = app(SyllabusService::class)->getAllSyllabiInSemesterAndClass($semester, $this->class);

        if ($this->syllabi->isEmpty()) {
            $this->syllabi = null;
        }
    }

    public function render()
    {
        $currentPage = $this->page ?? 1;
        $perPage = 10;
        $statringPoint = ($currentPage * $perPage) - $perPage;

        return view('livewire.list-syllabi-table', [
            'syllabiPaginated' => (new LengthAwarePaginator(($this->syllabi ?? collect())->slice($statringPoint, $perPage, true), collect($this->syllabi)->count(), $perPage, LengthAwarePaginator::resolveCurrentPage(), [LengthAwarePaginator::resolveCurrentPath()])),
        ]);
    }
}
