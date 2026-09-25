<?php

namespace App\Livewire;

use App\Http\Requests\UpdateFeeRequest;
use App\Models\Fee;
use App\Services\Fee\FeeService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditFeeForm extends Component
{
    #[Locked]
    public Fee $fee;

    public string $name = '';

    public string $description = '';

    public function mount(Fee $fee): void
    {
        Gate::authorize('update', $fee);

        $this->fee = $fee;
        $this->name = $fee->name;
        $this->description = $fee->description ?? '';
    }

    public function render(): View
    {
        return view('livewire.edit-fee-form');
    }

    public function save(FeeService $fees): void
    {
        Gate::authorize('update', $this->fee);

        $fees->updateFee($this->fee, $this->validate(UpdateFeeRequest::feeRules()));

        session()->flash('success', 'Fee Updated Successfully');

        $this->redirectRoute('fees.index');
    }
}
