<?php

namespace App\Http\Livewire;

use App\Services\Parent\ParentService;
use Livewire\Component;

class ListParentsTable extends Component
{
    /**
     * List of all parents in school.
     *
     * @var \App\Model\User
     */
    public $parents;

    public function mount(ParentService $parentService)
    {
        $this->parents = $parentService->getAllParents()->sortBy('name');
    }

    public function render()
    {
        return view('livewire.list-parents-table');
    }
}
