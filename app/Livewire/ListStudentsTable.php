<?php

namespace App\Livewire;

use Illuminate\Support\MessageBag;
use Livewire\Component;

class ListStudentsTable extends Component
{
    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.list-students-table');
    }
}
