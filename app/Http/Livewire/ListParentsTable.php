<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Parent\ParentService;


class ListParentsTable extends Component
{
    /**
     * List of all parents in school
     *
     * @var \App\Model\User
     */
    public $parents;

    public function mount(ParentService $parentService)
    {
        $this->parents = $parentService->getAllParents();
    }

    public function render()
    {
        return view('livewire.list-parents-table');
    }
}
