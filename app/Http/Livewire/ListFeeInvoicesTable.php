<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ListFeeInvoicesTable extends Component
{
    public $statuses = ['all', 'due', 'paid'];
    public $status;
    public $queryAddon;
    public $year;

    public function mount()
    {
        $this->year = date('Y');
        $this->queryAddon = [];
        $this->updatedStatus();
        $this->status = 'due';
    }

    public function updatedStatus()
    {
        switch ($this->status) {
            case 'all':
                $this->queryAddon = [];
                break;
            case 'due':
                $this->queryAddon = [['name' => 'isDue']];
                break;
            case 'paid':
                $this->queryAddon = [['name' => 'isPaid']];
                break;

            default:
                $this->queryAddon = [];
                break;
        }
    }

    public function render()
    {
        return view('livewire.list-fee-invoices-table');
    }
}
