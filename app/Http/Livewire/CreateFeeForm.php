<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\FeeCategory;

class CreateFeeForm extends Component
{
    public $feeCategories;
    public function mount()
    {
        $this->feeCategories = FeeCategory::where('school_id', auth()->user()->school_id)->get();
    }
    public function render()
    {
        return view('livewire.create-fee-form');
    }
}
