<?php

namespace App\Livewire;

use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\StudentRecord;
use App\Services\Fee\FeeInvoiceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Invoice the same fees to one student, a section, or a whole class.
 *
 * Every lookup stays inside the working school, so an id from another school
 * finds nothing. Each student gets their own invoice.
 */
class CreateFeeInvoiceForm extends Component
{
    public string $issueDate = '';

    public string $dueDate = '';

    public string $note = '';

    /** Sent with the batch so a double submit makes the invoices once. */
    public string $idempotencyKey = '';

    public string $academicLevelId = '';

    public string $cycleSectionId = '';

    public string $studentRecordId = '';

    /** @var array<int, int> */
    public array $studentRecordIds = [];

    public string $feeCategoryId = '';

    public string $feeId = '';

    /**
     * The fees on every invoice, keyed by fee id.
     *
     * @var array<int|string, array{amount: int|string|null, waiver: int|string|null, fine: int|string|null}>
     */
    public array $lines = [];

    public function mount(): void
    {
        Gate::authorize('create', FeeInvoice::class);

        $this->issueDate = school_today()->toDateString();
        $this->idempotencyKey = (string) Str::uuid();
        $this->academicLevelId = (string) ($this->levels()->first()?->id);
        $this->feeCategoryId = (string) (FeeCategory::inSchool()->orderBy('name')->value('id') ?? '');
    }

    public function updatedAcademicLevelId(): void
    {
        $this->reset('cycleSectionId', 'studentRecordId');
    }

    public function updatedCycleSectionId(): void
    {
        $this->reset('studentRecordId');
    }

    public function updatedFeeCategoryId(): void
    {
        $this->reset('feeId');
    }

    /**
     * Add the chosen student, or everyone in the chosen section or class.
     */
    public function addStudents(): void
    {
        $sectionIds = $this->sectionsQuery()
            ->when($this->cycleSectionId !== '', fn (Builder $query) => $query->whereKey((int) $this->cycleSectionId))
            ->pluck('id');

        $ids = $this->activeEnrollments($sectionIds)
            ->when($this->studentRecordId !== '', fn (Builder $query) => $query->whereKey((int) $this->studentRecordId))
            ->pluck('id')
            ->all();

        if ($ids === []) {
            $this->addError('studentRecordIds', 'No active students there.');

            return;
        }

        $this->resetErrorBag('studentRecordIds');
        $this->studentRecordIds = array_values(array_unique([...$this->studentRecordIds, ...$ids]));
    }

    public function removeStudent(int $studentRecordId): void
    {
        $this->studentRecordIds = array_values(array_diff($this->studentRecordIds, [$studentRecordId]));
    }

    public function clearStudents(): void
    {
        $this->studentRecordIds = [];
    }

    /**
     * Add the chosen fee, or every fee in the chosen category.
     */
    public function addFees(): void
    {
        $fees = $this->feesQuery()
            ->when($this->feeId !== '', fn (Builder $query) => $query->whereKey((int) $this->feeId))
            ->get(['fees.id']);

        foreach ($fees as $fee) {
            $this->lines[$fee->id] ??= ['amount' => null, 'waiver' => null, 'fine' => null];
        }

        $this->resetErrorBag('lines');
    }

    public function removeFee(int $feeId): void
    {
        unset($this->lines[$feeId]);
    }

    public function save(FeeInvoiceService $feeInvoiceService): void
    {
        Gate::authorize('create', FeeInvoice::class);

        $this->validate([
            'issueDate' => ['required', 'date'],
            'dueDate' => ['required', 'date', 'after_or_equal:issueDate'],
            'note' => ['nullable', 'string', 'max:10000'],
            'studentRecordIds' => ['required', 'array', 'min:1'],
            'studentRecordIds.*' => ['integer', Rule::exists('student_records', 'id')->where('school_id', current_school_id())->whereIn('status', [EnrollmentStatus::Active->value, EnrollmentStatus::Suspended->value])],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.amount' => ['required', 'integer', 'min:1', 'max:100000000'],
            'lines.*.waiver' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'lines.*.fine' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ], [
            'dueDate.after_or_equal' => 'The due date cannot be before the issue date.',
            'studentRecordIds.required' => 'Add at least one student.',
            'studentRecordIds.*.exists' => 'A student here is no longer active in this school.',
            'lines.required' => 'Add at least one fee.',
        ], [
            'issueDate' => 'issue date',
            'dueDate' => 'due date',
            'lines.*.amount' => 'amount',
            'lines.*.waiver' => 'waiver',
            'lines.*.fine' => 'fine',
        ]);

        foreach ($this->lines as $feeId => $line) {
            if ((int) ($line['waiver'] ?? 0) > (int) $line['amount']) {
                $this->addError("lines.{$feeId}.waiver", 'The waiver cannot be more than the amount.');
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        try {
            $feeInvoiceService->storeFeeInvoice([
                'idempotency_key' => $this->idempotencyKey,
                'issue_date' => $this->issueDate,
                'due_date' => $this->dueDate,
                'note' => trim($this->note) === '' ? null : trim($this->note),
                'student_records' => $this->studentRecordIds,
                'records' => collect($this->lines)->map(fn (array $line, int|string $feeId): array => [
                    'fee_id' => (int) $feeId,
                    'amount' => (int) $line['amount'],
                    'waiver' => (int) ($line['waiver'] ?? 0),
                    'fine' => (int) ($line['fine'] ?? 0),
                ])->values()->all(),
            ]);
        } catch (InvalidValueException $exception) {
            $this->addError('issueDate', $exception->getMessage());

            return;
        }

        $count = count($this->studentRecordIds);
        session()->flash('success', $count === 1 ? 'Invoice created.' : "{$count} invoices created.");
        $this->redirectRoute('fee-invoices.index');
    }

    public function render(): View
    {
        $students = StudentRecord::inSchool()
            ->whereKey($this->studentRecordIds)
            ->with(['user:id,name', 'academicCycleSection:id,name,label,academic_level_id', 'academicCycleSection.academicLevel:id,name'])
            ->get()
            ->sortBy(fn (StudentRecord $record): string => (string) $record->user?->name);
        $addedFees = Fee::query()
            ->whereIn('id', array_keys($this->lines))
            ->whereRelation('feeCategory', 'school_id', current_school_id())
            ->get(['id', 'name'])
            ->keyBy('id');
        $sections = $this->sectionsQuery()->orderBy('position')->orderBy('name')->get(['id', 'name', 'label']);

        $perInvoice = collect($this->lines)->sum(fn (array $line): int => max(0, (int) $line['amount'] - (int) $line['waiver']) + (int) $line['fine']);

        return view('livewire.create-fee-invoice-form', [
            'levels' => $this->levels(),
            'sections' => $sections,
            'studentsToPick' => $this->cycleSectionId === '' ? collect() : $this->activeEnrollments(collect([(int) $this->cycleSectionId]))->with('user:id,name')->get()->sortBy(fn (StudentRecord $record): string => (string) $record->user?->name),
            'students' => $students,
            'categories' => FeeCategory::inSchool()->orderBy('name')->get(['id', 'name']),
            'feesToPick' => $this->feesQuery()->orderBy('name')->get(['fees.id', 'fees.name']),
            'addedFees' => $addedFees,
            'perInvoice' => $perInvoice,
        ]);
    }

    /**
     * @return Collection<int, AcademicLevel>
     */
    private function levels(): Collection
    {
        return AcademicLevel::inSchool()
            ->where('is_group', false)
            ->whereHas('cycleSections', fn (Builder $query) => $query->where('academic_year_id', current_academic_year_id()))
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return Builder<AcademicCycleSection>
     */
    private function sectionsQuery(): Builder
    {
        return AcademicCycleSection::inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->where('academic_level_id', (int) $this->academicLevelId);
    }

    /**
     * @param  Collection<int, mixed>  $sectionIds
     * @return Builder<StudentRecord>
     */
    private function activeEnrollments(Collection $sectionIds): Builder
    {
        return StudentRecord::inSchool()
            ->enrolled()
            ->whereIn('academic_cycle_section_id', $sectionIds);
    }

    /**
     * @return Builder<Fee>
     */
    private function feesQuery(): Builder
    {
        return Fee::query()
            ->where('fee_category_id', (int) $this->feeCategoryId)
            ->whereRelation('feeCategory', 'school_id', current_school_id());
    }
}
