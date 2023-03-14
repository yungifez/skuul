<?php

namespace App\Http\Livewire;

use App\Models\Fee;
use Livewire\Component;

class EditFeeForm extends Component
{
    public Fee $fee;

    public function render()
    {
        return view('livewire.edit-fee-form');
    }
}
