<?php

namespace App\Livewire;

use App\Models\ClassGroup;
use Livewire\Component;

class ShowClassGroup extends Component
{
    public ClassGroup $classGroup;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.show-class-group');
    }
}
