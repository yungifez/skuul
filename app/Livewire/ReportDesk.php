<?php

namespace App\Livewire;

use App\Actions\Report\RequestReport;
use App\Enums\ReportStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\FinancialPeriod;
use App\Models\ReportRun;
use App\Services\Report\ExportFormatRegistry;
use App\Services\Report\ReportRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Ask for reports and collect the files.
 *
 * A worker builds each report, so the list keeps checking until nothing is
 * still on its way. A person only sees and asks for reports about data they
 * may read on screen.
 */
class ReportDesk extends Component
{
    use DispatchesStatusNotifications;
    use WithPagination;

    public string $type = '';

    public string $format = 'csv';

    public string $financialPeriodId = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', ReportRun::class);
    }

    public function build(RequestReport $requestReport, ReportRegistry $reportRegistry, ExportFormatRegistry $formats): void
    {
        Gate::authorize('create', ReportRun::class);

        $this->validate([
            'type' => ['required', Rule::in(array_keys($reportRegistry->availableTo(auth()->user())))],
            'format' => ['required', Rule::in(array_keys($formats->all()))],
            'financialPeriodId' => ['nullable', 'integer', Rule::exists((new FinancialPeriod)->getTable(), 'id')->where('school_id', current_school_id())],
        ], [
            'type.in' => 'Choose a report about data you can read.',
        ], ['type' => 'report', 'format' => 'shape', 'financialPeriodId' => 'financial period']);

        try {
            $run = $requestReport->request(
                type: $this->type,
                parameters: $this->financialPeriodId === '' ? [] : ['financial_period_id' => (int) $this->financialPeriodId],
                actor: auth()->user(),
                format: $this->format,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('type', $exception->getMessage());

            return;
        }

        $this->resetPage();
        $this->notify("The report is being built. It is number $run->id.");
    }

    public function render(ReportRegistry $reportRegistry, ExportFormatRegistry $formats): View
    {
        $readable = $reportRegistry->availableTo(auth()->user());

        $runs = ReportRun::query()
            ->inSchool()
            ->whereIn('type', array_keys($readable))
            ->with('requestedBy:id,name')
            ->latest('id')
            ->paginate(20);

        return view('livewire.report-desk', [
            'runs' => $runs,
            'reports' => $readable,
            'formats' => $formats->all(),
            'canRequest' => Gate::allows('create', ReportRun::class) && $readable !== [],
            'isBuilding' => $runs->getCollection()->contains(
                fn (ReportRun $run): bool => in_array($run->status, [ReportStatus::Queued, ReportStatus::Running], true),
            ),
            'financialPeriods' => FinancialPeriod::query()->inSchool()->orderByDesc('starts_on')->get(),
        ]);
    }
}
