<?php

namespace App\Actions\Report;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Jobs\BuildReport;
use App\Models\ReportRun;
use App\Models\User;
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
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Request the report.
     *
     * @param  array<string, mixed>  $parameters
     */
    public function request(string $type, array $parameters = [], ?User $actor = null): ReportRun
    {
        // Fail here, not inside the worker, when the name is wrong.
        $report = $this->registry->get($type);

        $run = ReportRun::create([
            'school_id' => current_school_id(),
            'type' => $report->key(),
            'parameters' => $parameters === [] ? null : $parameters,
            'academic_year_id' => current_academic_year_id(),
            'semester_id' => current_semester_id(),
            'requested_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        BuildReport::dispatch($run->id);

        $this->auditor->record(
            AuditAction::ReportRequested,
            $run,
            ['type' => $run->type, 'parameters' => $parameters],
            $actor,
        );

        return $run;
    }
}
