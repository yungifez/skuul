<?php

namespace App\Http\Livewire;

use App\Services\Timetable\TimetableService;
use Livewire\Component;

class ListCustomTimetableItemsTable extends Component
{
    public $customTimetableItems;

    public function mount(TimetableService $timetableService)
    {
        $this->customTimetableItems = $timetableService->getAllCustomTimetableItem();
    }
    public function render()
    {
        return view('livewire.list-custom-timetable-items-table');
    }
}
