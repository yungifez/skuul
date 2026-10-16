<?php

namespace App\Livewire;

use App\Http\Requests\StoreFeeCategoryRequest;
use App\Models\FeeCategory;
use App\Services\Fee\FeeCategoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class CreateFeeCategoryForm extends Component
{
    public string $name = '';

    public string $description = '';

    public function render(): View
    {
        return view('livewire.create-fee-category-form');
    }

    public function save(FeeCategoryService $feeCategories): void
    {
        Gate::authorize('create', FeeCategory::class);

        $validated = $this->validate(StoreFeeCategoryRequest::feeCategoryRules());
        $validated['school_id'] = current_school_id();

        $feeCategories->storeFeeCategory($validated);

        session()->flash('success', 'Fee Category Successfully Created');

        $this->redirectRoute('fee-categories.index');
    }
}
