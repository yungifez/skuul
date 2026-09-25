<?php

namespace App\Livewire;

use App\Http\Requests\StoreFeeCategoryRequest;
use App\Models\FeeCategory;
use App\Services\Fee\FeeCategoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditFeeCategoryForm extends Component
{
    #[Locked]
    public FeeCategory $feeCategory;

    public string $name = '';

    public string $description = '';

    public function mount(FeeCategory $feeCategory): void
    {
        Gate::authorize('update', $feeCategory);

        $this->feeCategory = $feeCategory;
        $this->name = $feeCategory->name;
        $this->description = $feeCategory->description ?? '';
    }

    public function render(): View
    {
        return view('livewire.edit-fee-category-form');
    }

    public function save(FeeCategoryService $feeCategories): void
    {
        Gate::authorize('update', $this->feeCategory);

        $validated = $this->validate(StoreFeeCategoryRequest::feeCategoryRules());

        $feeCategories->updateFeeCategory($this->feeCategory, $validated);

        session()->flash('success', 'Fee Category Updated Successfully');

        $this->redirectRoute('fee-categories.index');
    }
}
