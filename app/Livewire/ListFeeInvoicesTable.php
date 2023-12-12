<?php

namespace App\Livewire;

use Livewire\Component;

class ListFeeInvoicesTable extends Component
{
    protected $queryString = ['status'];
    public $statuses = ['all', 'due', 'paid'];
    public $status;
    public $queryAddon;
    public $year;

    public function mount()
    {
        $this->year = date('Y');
        $this->queryAddon = [];
        $this->status = $this->status ?? 'due';
        $this->updatedStatus();
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
                $this->status = 'all';
                $this->queryAddon = [];
                break;
        }
    }

    public function render()
    {
        return view('livewire.list-fee-invoices-table');
    }
}
