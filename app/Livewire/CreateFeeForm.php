<?php

namespace App\Livewire;

use App\Http\Requests\StoreFeeRequest;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Services\Fee\FeeService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class CreateFeeForm extends Component
{
    /** @var list<FeeCategory> */
    public array $feeCategories = [];

    public string $name = '';

    public string $description = '';

    public ?int $fee_category_id = null;

    public function mount(FeeService $fees): void
    {
        Gate::authorize('create', Fee::class);

        $this->feeCategories = $fees->feeCategoriesForWorkingSchool()->all();
        $this->fee_category_id = $this->feeCategories[0]->id ?? null;
    }

    public function render(): View
    {
        return view('livewire.create-fee-form');
    }

    public function save(FeeService $fees): void
    {
        Gate::authorize('create', Fee::class);

        $validated = $this->validate(StoreFeeRequest::feeRulesForCurrentSchool());

        $fees->storeFee($validated);

        session()->flash('success', 'Fee Created Successfully');

        $this->redirectRoute('fees.index');
    }
}
