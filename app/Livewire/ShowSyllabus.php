<?php

namespace App\Livewire;

use App\Models\Syllabus;
use Livewire\Component;

class ShowSyllabus extends Component
{
    public Syllabus $syllabus;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.show-syllabus');
    }
}
