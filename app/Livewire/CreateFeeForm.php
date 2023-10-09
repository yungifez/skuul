<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use Livewire\Component;

class CreateFeeForm extends Component
{
    public $feeCategories;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

        $this->feeCategories = FeeCategory::where('school_id', auth()->user()->school_id)->get();
    }

    public function render()
    {
        return view('livewire.create-fee-form');
    }
}
