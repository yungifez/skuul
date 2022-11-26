<?php

namespace App\Http\Livewire;

use App\Models\Weekday;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;
use App\Services\Timetable\TimetableService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTimetablesTable extends Component
{
    use WithPagination;
    public $class;
    public $timetables;
    public $classes;
    public $weekdays;
    protected $queryString = ['page'];
    protected $paginationTheme = 'bootstrap';


    public function mount(TimetableService $timetableService, MyClassService $myClassService)
    {
        //get current semester
        $semester = auth()->user()->school->semester_id;
        //check if user is a student
        if (auth()->user()->hasRole('student')) {
            // get student class and set class name
            $this->class = auth()->user()->studentRecord->myClass->name;
            $class = auth()->user()->studentRecord->my_class_id;
            //get timetables in semester and class
            $this->timetables = $timetableService->getAllTimetablesInSemesterAndClass($semester, $class);
        }
        //user isn't a student
        else {
            //get all classes
            $this->classes = $myClassService->getAllClasses();
            //set initial record
            if (!$this->classes->isEmpty()) {
                $this->timetables = $timetableService->getAllTimetablesInSemesterAndClass($semester, $this->classes[0]['id']);
            } else {
                $this->timetables = collect([]);
            }
        }

        if ($this->timetables->isEmpty()) {
            $this->timetables = collect();
        }

        $this->weekdays = Weekday::all();
    }

    public function updatedClass()
    {
        //get current semester
        $semester = auth()->user()->school->semester_id;
        //get timetables in semester and class
        $this->timetables = app(TimetableService::class)->getAllTimetablesInSemesterAndClass($semester, $this->class);

        if ($this->timetables->isEmpty()) {
            $this->timetables = collect();
        }
    }

    public function render()
    {
        $currentPage  = $this->page ?? 1;
        $perPage = 10;
        $statringPoint = ($currentPage * $perPage) - $perPage;
        return view('livewire.list-timetables-table', [
            'timetablesPaginated' => (new LengthAwarePaginator(($this->timetables ?? collect())->slice($statringPoint, $perPage,true ) ,collect( $this->timetables)->count(), $perPage, LengthAwarePaginator::resolveCurrentPage(), [LengthAwarePaginator::resolveCurrentPath()]))
        ]);
    }
}
