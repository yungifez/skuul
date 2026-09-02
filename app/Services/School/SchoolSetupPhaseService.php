<?php

namespace App\Services\School;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SchoolSetupPhaseStatus;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolSetupPhase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SchoolSetupPhaseService
{
    public const PHASE_VERSION = 1;

    public function __construct(
        private SchoolSetupChecklist $checklist,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Reconcile the current required setup with its persisted phase.
     *
     * A phase is scoped to the checklist version and current academic year, so
     * a new year or a future setup phase can have its own acknowledgement.
     *
     * @return array<string, mixed>
     */
    public function for(School $school): array
    {
        $checklist = $this->checklist->for($school);
        $academicYear = $checklist['academicYear'];
        $phaseKey = $this->phaseKey($academicYear);
        $phase = $school->setupPhases()->firstOrCreate(
            ['phase_key' => $phaseKey],
            [
                'academic_year_id' => $academicYear?->id,
                'status' => SchoolSetupPhaseStatus::InProgress,
            ],
        );

        $this->reconcile($phase, $checklist['required_remaining'] === 0);
        $phase->refresh();

        return [
            ...$checklist,
            'phase' => $phase,
            'phase_key' => $phaseKey,
            'show_ready_notice' => $phase->status === SchoolSetupPhaseStatus::Ready,
            'show_dashboard_card' => $checklist['required_remaining'] > 0
                || $phase->status === SchoolSetupPhaseStatus::Ready,
        ];
    }

    /**
     * Acknowledge the ready notice for the current setup phase.
     */
    public function acknowledge(School $school, User $actor): bool
    {
        return DB::transaction(function () use ($school, $actor): bool {
            $state = $this->for($school);
            $phase = $state['phase'];

            if (!$state['show_ready_notice'] || !$phase instanceof SchoolSetupPhase) {
                return false;
            }

            $phase->forceFill([
                'status' => SchoolSetupPhaseStatus::Acknowledged,
                'acknowledged_at' => now(),
                'acknowledged_by' => $actor->id,
            ])->save();

            $this->auditor->record(
                AuditAction::SchoolSetupAcknowledged,
                $phase,
                [
                    'phase_key' => $phase->phase_key,
                    'academic_year_id' => $phase->academic_year_id,
                ],
                $actor,
            );

            return true;
        }, attempts: 3);
    }

    private function phaseKey(?AcademicYear $academicYear): string
    {
        $academicYearId = $academicYear === null ? 'none' : $academicYear->id;

        return 'daily-work-v'.self::PHASE_VERSION.':academic-year-'.$academicYearId;
    }

    private function reconcile(SchoolSetupPhase $phase, bool $requiredSetupComplete): void
    {
        if (!$requiredSetupComplete) {
            if ($phase->status !== SchoolSetupPhaseStatus::InProgress
                || $phase->completed_at !== null
                || $phase->acknowledged_at !== null
                || $phase->acknowledged_by !== null) {
                $phase->forceFill([
                    'status' => SchoolSetupPhaseStatus::InProgress,
                    'completed_at' => null,
                    'acknowledged_at' => null,
                    'acknowledged_by' => null,
                ])->save();
            }

            return;
        }

        if ($phase->status === SchoolSetupPhaseStatus::InProgress) {
            $phase->forceFill([
                'status' => SchoolSetupPhaseStatus::Ready,
                'completed_at' => now(),
            ])->save();
        }
    }
}
