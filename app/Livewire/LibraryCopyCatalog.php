<?php

namespace App\Livewire;

use App\Enums\Feature;
use App\Models\LibraryCopy;
use App\Services\Library\LibraryCopyCatalog as LibraryCopyCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LibraryCopyCatalog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    protected LibraryCopyCatalogService $catalog;

    public function boot(LibraryCopyCatalogService $catalog): void
    {
        abort_unless(features()->enabled(Feature::Library), 404);
        Gate::authorize('viewAny', LibraryCopy::class);

        $this->catalog = $catalog;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render(): View
    {
        return ViewFactory::make('livewire.library-copy-catalog', [
            'copies' => $this->catalog->search($this->search),
            'canManage' => auth()->user()?->can('manage library') === true,
        ]);
    }
}
