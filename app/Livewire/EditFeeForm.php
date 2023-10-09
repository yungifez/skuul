<?php

namespace App\Livewire;

use App\Models\Fee;
use Livewire\Component;

class EditFeeForm extends Component
{
    public Fee $fee;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-fee-form');
    }
}
