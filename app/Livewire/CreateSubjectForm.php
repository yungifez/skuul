<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class CreateSubjectForm extends Component
{
    public bool $setup = false;

    public ?int $academicYearId = null;

    public function mount(bool $setup = false, ?int $academicYearId = null): void
    {
        $this->setup = $setup;
        $this->academicYearId = $academicYearId;
    }

    public function render(): View
    {
        return view('livewire.create-subject-form');
    }
}
