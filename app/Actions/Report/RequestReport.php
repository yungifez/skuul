<?php

namespace App\Actions\Report;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\ReportStatus;
use App\Exceptions\InvalidValueException;
use App\Jobs\BuildReport;
use App\Models\FinancialPeriod;
use App\Models\ReportRun;
use App\Models\User;
use App\Services\Report\ExportFormatRegistry;
use App\Services\Report\ReportRegistry;

/**
 * Ask for a report and let a worker build it.
 *
 * The request is recorded before anything is built, so a report that fails
 * still says who asked for it and why it failed.
 */
class RequestReport
{
    public function __construct(
        private ReportRegistry $registry,
        private ExportFormatRegistry $formats,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Request the report.
     *
     * Asking again for the same report while it is still being built hands
     * back the run already on its way.
     *
     * @param  array<string, mixed>  $parameters
     *
     * @throws InvalidValueException when the report is unknown or not the person's to read
     */
    public function request(string $type, array $parameters = [], ?User $actor = null, string $format = 'csv'): ReportRun
    {
        // Fail here, not inside the worker, when a name is wrong.
        $report = $this->registry->get($type);
        $shape = $this->formats->get($format);
        $actor ??= auth()->user();

        if ($actor instanceof User && !$actor->can($report->permission())) {
            throw new InvalidValueException("You cannot read {$report->title()}, so you cannot ask for it.");
        }
        $period = isset($parameters['financial_period_id'])
            ? FinancialPeriod::query()->inSchool()->find($parameters['financial_period_id'])
            : FinancialPeriod::query()->inSchool()->open()->orderByDesc('starts_on')->first();

        $alreadyOnItsWay = ReportRun::query()
            ->inSchool()
            ->where('type', $report->key())
            ->where('format', $shape->key())
            ->where('requested_by', $actor?->id)
            ->where('financial_period_id', $period?->id)
            ->whereIn('status', [ReportStatus::Queued, ReportStatus::Running])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->latest('id')
            ->first();

        if ($alreadyOnItsWay !== null && $alreadyOnItsWay->parameters === ($parameters === [] ? null : $parameters)) {
            return $alreadyOnItsWay;
        }

        $run = ReportRun::create([
            'school_id' => current_school_id(),
            'type' => $report->key(),
            'format' => $shape->key(),
            'parameters' => $parameters === [] ? null : $parameters,
            'academic_year_id' => current_academic_year_id(),
            'academic_period_id' => current_academic_period_id(),
            'financial_period_id' => $period?->id,
            'requested_by' => $actor?->id,
        ]);

        BuildReport::dispatch($run->id);

        $this->auditor->record(
            AuditAction::ReportRequested,
            $run,
            ['type' => $run->type, 'format' => $run->format, 'parameters' => $parameters],
            $actor,
        );

        return $run;
    }
}
