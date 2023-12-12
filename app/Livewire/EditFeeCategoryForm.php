<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use Livewire\Component;

class EditFeeCategoryForm extends Component
{
    public FeeCategory $feeCategory;

    public function render()
    {
        return view('livewire.edit-fee-category-form');
    }
}
