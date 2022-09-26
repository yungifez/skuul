<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DeleteModal extends Component
{
    public $modal_id;
    public $button_label = 'Delete';
    public $action;
    public $item_name;
    public $button_class;

    public function render()
    {
        return view('livewire.delete-modal');
    }
}
