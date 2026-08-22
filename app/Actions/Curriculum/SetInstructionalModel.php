<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\InstructionalModel;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\InstructionalModelSetting;
use App\Models\User;
use App\Services\Curriculum\InstructionalModelResolver;
use Illuminate\Support\Facades\DB;

/**
 * Choose the way a campus teaches an academic cycle.
 *
 * The choice is only a setting before the cycle starts. Once learners,
 * timetables, and results exist against it, changing the model would rewrite
 * what already happened, so this action refuses. A cycle that is running
 * needs the audited migration workflow instead.
 */
class SetInstructionalModel
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private InstructionalModelResolver $resolver,
    ) {
    }

    /**
     * Record the model the campus teaches the cycle with.
     *
     * Asking for the model the cycle already has changes nothing and adds no
     * second audit record.
     *
     * @throws InvalidValueException when the cycle has started or finished
     */
    public function set(
        AcademicYear $academicYear,
        InstructionalModel $model,
        ?User $actor = null,
        ?string $reason = null,
    ): InstructionalModelSetting {
        $this->refuseCycleThatStarted($academicYear);

        $setting = $this->resolver->settingFor($academicYear);
        $current = $setting?->model;

        if ($setting !== null && $current === $model) {
            return $setting;
        }

        return DB::transaction(function () use ($academicYear, $model, $actor, $reason, $current, $setting): InstructionalModelSetting {
            $setting = InstructionalModelSetting::updateOrCreate(
                [
                    'school_id'        => $academicYear->school_id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'model'      => $model,
                    'updated_by' => $actor === null ? auth()->id() : $actor->id,
                ],
            );

            $this->resolver->forget();

            $this->auditor->record(
                AuditAction::InstructionalModelChanged,
                $setting,
                [
                    'from'             => $current?->value,
                    'to'               => $model->value,
                    'school_id'        => $academicYear->school_id,
                    'academic_year_id' => $academicYear->id,
                    'reason'           => $reason,
                ],
                $actor,
            );

            return $setting;
        });
    }

    /**
     * Check if the cycle is still far enough ahead to be a setting.
     *
     * A cycle is future while it is being planned and has not reached its
     * first day. The calendar lifecycle answers this; there is no second set
     * of dates to keep in step.
     */
    public function isFutureCycle(AcademicYear $academicYear): bool
    {
        if ($academicYear->status->isOperational() || $academicYear->status->isFrozen()) {
            return false;
        }

        return $academicYear->starts_on === null
            || $academicYear->starts_on->startOfDay()->isAfter(now()->startOfDay());
    }

    /**
     * Stop a change to a cycle that learners already work in.
     *
     * @throws InvalidValueException
     */
    private function refuseCycleThatStarted(AcademicYear $academicYear): void
    {
        if ($this->isFutureCycle($academicYear)) {
            return;
        }

        throw new InvalidValueException(
            "The instructional model of {$academicYear->name} cannot change here, because the cycle has already started. "
            .'Choose the model for a cycle that has not started, or move this one with the audited migration workflow.'
        );
    }
}
