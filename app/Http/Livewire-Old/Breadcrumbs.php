<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Breadcrumbs extends Component
{
    public $active;

    public $paths;

    public function render()
    {
        return view('livewire.breadcrumbs');
    }
}
