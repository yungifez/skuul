<?php

namespace App\Livewire;

use App\Actions\Library\IssueLoan;
use App\Actions\Library\IssueTitleToSection;
use App\Actions\Library\RenewLoan;
use App\Actions\Library\ReturnLoan;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The lending desk: scan a copy, then lend it or take it back.
 *
 * A scan decides what the desk offers. A copy that is out can only come back
 * or be renewed; a copy on the shelf can only go to somebody who belongs to
 * this campus.
 */
class LibraryLendingDesk extends Component
{
    use DispatchesStatusNotifications;

    public string $barcode = '';

    #[Locked]
    public ?int $scannedCopyId = null;

    public string $borrowerSearch = '';

    public string $outSearch = '';

    public bool $isLendingSet = false;

    public string $sectionId = '';

    public string $titleId = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', LibraryLoan::class);
    }

    public function scan(): void
    {
        $this->resetValidation();
        $this->reset('scannedCopyId', 'borrowerSearch');

        $barcode = trim($this->barcode);

        if ($barcode === '') {
            $this->addError('barcode', 'Scan or type the barcode.');

            return;
        }

        $copy = LibraryCopy::query()->inSchool()->where('barcode', $barcode)->first();

        if ($copy === null) {
            $this->addError('barcode', 'No copy on this campus has that barcode.');

            return;
        }

        $this->scannedCopyId = $copy->id;
    }

    public function clearScan(): void
    {
        $this->reset('barcode', 'scannedCopyId', 'borrowerSearch');
        $this->resetValidation();
    }

    public function lendTo(int $userId, IssueLoan $issueLoan): void
    {
        Gate::authorize('create', LibraryLoan::class);

        $copy = $this->scannedCopy();
        $borrower = User::query()->ofSchool()->find($userId);

        if ($copy === null) {
            $this->addError('barcode', 'Scan the copy again.');

            return;
        }

        if ($borrower === null) {
            $this->addError('borrowerSearch', 'This person does not belong to this campus.');

            return;
        }

        try {
            $loan = $issueLoan->issue($copy, $borrower, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('borrowerSearch', $exception->getMessage());

            return;
        }

        $this->clearScan();
        $this->notify("{$copy->title?->title} is out to {$borrower->name} until {$loan->due_on->format('j M')}.");
    }

    public function takeBack(int $loanId, ReturnLoan $returnLoan): void
    {
        $loan = $this->loan($loanId);
        Gate::authorize('update', $loan);

        try {
            $loan = $returnLoan->receive($loan, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        if ($this->scannedCopyId === $loan->library_copy_id) {
            $this->clearScan();
        }

        $this->notify($loan->fine_charged > 0
            ? 'The copy is back. A fine of '.$loan->fine()->formatToLocale(app()->getLocale()).' went on the account.'
            : 'The copy is back.');
    }

    public function renew(int $loanId, RenewLoan $renewLoan): void
    {
        $loan = $this->loan($loanId);
        Gate::authorize('update', $loan);

        try {
            $loan = $renewLoan->renew($loan, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('Due back '.$loan->due_on->format('j M Y').'.');
    }

    public function lendSet(IssueTitleToSection $issueTitleToSection): void
    {
        Gate::authorize('create', LibraryLoan::class);

        $this->validate([
            'sectionId' => ['required', 'integer'],
            'titleId' => ['required', 'integer'],
        ], [
            'sectionId.required' => 'Choose the class.',
            'titleId.required' => 'Choose the title.',
        ]);

        $section = AcademicCycleSection::query()->inSchool()->find((int) $this->sectionId);
        $title = $this->titlesOnTheShelf()->firstWhere('id', (int) $this->titleId);

        if ($section === null || $title === null) {
            $this->addError('sectionId', 'Choose a class and a title from the lists.');

            return;
        }

        try {
            $loans = $issueTitleToSection->issue($section, $title, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('sectionId', $exception->getMessage());

            return;
        }

        $this->reset('isLendingSet', 'sectionId', 'titleId');
        $this->notify("{$loans->count()} copies of {$title->title} are out to the class.");
    }

    public function render(): View
    {
        $copy = $this->scannedCopy();
        $search = trim($this->outSearch);

        return view('livewire.library-lending-desk', [
            'rules' => LibraryLendingRules::forSchool(),
            'copy' => $copy,
            'copyLoan' => $copy === null ? null : LibraryLoan::query()->inSchool()->open()->where('library_copy_id', $copy->id)->with('borrower:id,name')->first(),
            'borrowers' => $copy === null ? collect() : $this->matchingBorrowers(),
            'open' => LibraryLoan::query()->inSchool()->open()
                ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $match) => $match
                    ->whereHas('borrower', fn (Builder $borrower) => $borrower->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('copy', fn (Builder $copies) => $copies->where('barcode', 'like', "%{$search}%")
                        ->orWhereHas('title', fn (Builder $titles) => $titles->where('title', 'like', "%{$search}%")))))
                ->with(['copy:id,barcode,library_title_id', 'copy.title:id,title', 'borrower:id,name'])
                ->orderBy('due_on')
                ->limit(100)
                ->get(),
            'openCount' => LibraryLoan::query()->inSchool()->open()->count(),
            'overdueCount' => LibraryLoan::query()->inSchool()->overdue()->count(),
            'returned' => LibraryLoan::query()->inSchool()->whereNotNull('returned_on')
                ->with(['copy:id,library_title_id', 'copy.title:id,title', 'borrower:id,name'])
                ->orderByDesc('returned_on')
                ->orderByDesc('id')
                ->limit(10)
                ->get(),
            'sections' => $this->isLendingSet
                ? AcademicCycleSection::query()->inSchool()->with(['academicLevel:id,name', 'academicYear'])->orderBy('academic_year_id')->orderBy('academic_level_id')->orderBy('position')->get()
                : collect(),
            'titles' => $this->isLendingSet ? $this->titlesOnTheShelf() : collect(),
            'canLend' => Gate::allows('create', LibraryLoan::class),
        ]);
    }

    private function scannedCopy(): ?LibraryCopy
    {
        return $this->scannedCopyId === null
            ? null
            : LibraryCopy::query()->inSchool()->with('title:id,title')->find($this->scannedCopyId);
    }

    private function loan(int $loanId): LibraryLoan
    {
        return LibraryLoan::query()->inSchool()->findOrFail($loanId);
    }

    /**
     * People of this campus whose name or admission number matches.
     *
     * @return Collection<int, User>
     */
    private function matchingBorrowers(): Collection
    {
        $search = trim($this->borrowerSearch);

        if (mb_strlen($search) < 2) {
            return collect();
        }

        return User::query()
            ->ofSchool()
            ->where(fn (Builder $match) => $match
                ->where('name', 'like', "%{$search}%")
                ->orWhereHas('studentRecords', fn (Builder $enrollment) => $enrollment
                    ->where('school_id', current_school_id())
                    ->where('admission_number', 'like', "%{$search}%")))
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name'])
            ->toBase();
    }

    /**
     * @return Collection<int, LibraryTitle>
     */
    private function titlesOnTheShelf(): Collection
    {
        return LibraryTitle::forSchool()
            ->whereHas('copies', fn ($query) => $query->where('school_id', current_school_id()))
            ->orderBy('title')
            ->get(['id', 'title'])
            ->toBase();
    }
}
