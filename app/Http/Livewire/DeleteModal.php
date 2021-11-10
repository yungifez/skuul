<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DeleteModal extends Component
{
    public $modal_id;
    public $action;
    public $item_name;
    public function render()
    {
        return view('livewire.delete-modal');
    }
}
