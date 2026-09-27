<?php

namespace App\Livewire;

use App\Actions\Library\ShelveLibraryCopies;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\LibraryCopy;
use App\Models\LibraryTitle;
use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Put a book on this campus's shelf.
 *
 * The librarian finds the book in the group's catalogue, or describes a new
 * one, then scans the barcode of the first copy.
 */
class LibraryShelvingForm extends Component
{
    use DispatchesStatusNotifications;

    public bool $isShelving = false;

    public string $titleSearch = '';

    #[Locked]
    public ?int $titleId = null;

    public bool $isDescribing = false;

    public string $title = '';

    public string $authors = '';

    public string $isbn = '';

    public string $category = '';

    public string $barcode = '';

    public string $copies = '1';

    public string $shelfMark = '';

    public function mount(): void
    {
        Gate::authorize('create', LibraryCopy::class);
    }

    public function start(): void
    {
        $this->isShelving = true;
    }

    public function stop(): void
    {
        $this->reset();
        $this->resetValidation();
    }

    public function pickTitle(int $titleId): void
    {
        $this->titleId = LibraryTitle::forSchool()->findOrFail($titleId)->id;
        $this->isDescribing = false;
        $this->resetValidation('titleSearch');
    }

    public function forgetTitle(): void
    {
        $this->reset('titleId');
    }

    public function describeNew(): void
    {
        $this->isDescribing = true;
        $this->titleId = null;
        $this->title = trim($this->titleSearch);
    }

    public function save(ShelveLibraryCopies $shelveLibraryCopies): void
    {
        Gate::authorize('create', LibraryCopy::class);

        $this->validate([
            'title' => [$this->isDescribing ? 'required' : 'nullable', 'string', 'max:255'],
            'authors' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'category' => ['nullable', 'string', 'max:80'],
            'barcode' => ['required', 'string', 'max:60'],
            'copies' => ['required', 'integer', 'min:1', 'max:50'],
            'shelfMark' => ['nullable', 'string', 'max:60'],
        ], [], ['shelfMark' => 'shelf mark']);

        $title = $this->titleId === null ? null : LibraryTitle::forSchool()->find($this->titleId);

        if ($title === null && !$this->isDescribing) {
            $this->addError('titleSearch', 'Find the book, or describe a new one.');

            return;
        }

        try {
            $made = $shelveLibraryCopies->shelve(
                School::query()->findOrFail(current_school_id()),
                $title,
                $this->isDescribing ? [
                    'title' => trim($this->title),
                    'authors' => $this->nullIfBlank($this->authors),
                    'isbn' => $this->nullIfBlank($this->isbn),
                    'category' => $this->nullIfBlank($this->category),
                ] : null,
                trim($this->barcode),
                (int) $this->copies,
                $this->nullIfBlank($this->shelfMark),
            );
        } catch (InvalidValueException $exception) {
            $this->addError(str_contains($exception->getMessage(), 'ISBN') ? 'isbn' : 'barcode', $exception->getMessage());

            return;
        }

        $this->stop();
        $this->dispatch('library-copies-shelved');
        $this->notify($made->count() === 1 ? 'The copy is on the shelf.' : "{$made->count()} copies are on the shelf.");
    }

    public function render(): View
    {
        return view('livewire.library-shelving-form', [
            'picked' => $this->titleId === null ? null : LibraryTitle::forSchool()->find($this->titleId),
            'matches' => $this->isShelving && $this->titleId === null && !$this->isDescribing ? $this->matchingTitles() : collect(),
        ]);
    }

    /**
     * Books in the group's catalogue whose title, author or ISBN matches.
     *
     * @return Collection<int, LibraryTitle>
     */
    private function matchingTitles(): Collection
    {
        $search = trim($this->titleSearch);

        if (mb_strlen($search) < 2) {
            return collect();
        }

        return LibraryTitle::forSchool()
            ->where(fn (Builder $match) => $match
                ->where('title', 'like', "%{$search}%")
                ->orWhere('authors', 'like', "%{$search}%")
                ->orWhere('isbn', $search))
            ->orderBy('title')
            ->limit(8)
            ->get(['id', 'title', 'authors'])
            ->toBase();
    }

    private function nullIfBlank(string $value): ?string
    {
        return trim($value) === '' ? null : trim($value);
    }
}
