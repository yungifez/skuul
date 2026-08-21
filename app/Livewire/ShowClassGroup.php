<?php

namespace App\Livewire;

use App\Models\ClassGroup;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class ShowClassGroup extends Component
{
    public ClassGroup $classGroup;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.show-class-group');
    }
}
