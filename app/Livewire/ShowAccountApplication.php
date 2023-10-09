<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowAccountApplication extends Component
{
    public User $applicant;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

        $this->applicant->loadMissing('accountApplication.statuses');
    }

    public function render()
    {
        return view('livewire.show-account-application');
    }
}
