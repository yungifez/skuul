<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use Livewire\Component;

class EditFeeCategoryForm extends Component
{
    public FeeCategory $feeCategory;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-fee-category-form');
    }
}
