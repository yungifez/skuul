<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingResidence;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LinkSchoolToBoardingResidence
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Let one campus use the physical residence.
     *
     * The campus must belong to the same organization as the residence.
     */
    public function link(BoardingResidence $residence, School $school, ?User $actor = null): void
    {
        if ($school->organization_id !== $residence->organization_id) {
            throw new InvalidValueException('That campus belongs to another organization.');
        }

        DB::transaction(function () use ($residence, $school, $actor): void {
            if ($residence->schools()->whereKey($school->id)->exists()) {
                return;
            }

            $residence->schools()->attach($school->id, [
                'linked_by' => $actor !== null ? $actor->id : auth()->id(),
            ]);

            $this->auditor->record(
                AuditAction::BoardingResidenceChanged,
                $residence,
                ['change' => 'campus_linked', 'school_id' => $school->id],
                $actor,
            );
        });
    }

    /**
     * Remove a campus when none of its houses still use the residence.
     */
    public function unlink(BoardingResidence $residence, School $school, ?User $actor = null): void
    {
        if ($school->organization_id !== $residence->organization_id) {
            throw new InvalidValueException('That campus belongs to another organization.');
        }

        if ($residence->dormitories()->where('school_id', $school->id)->exists()) {
            throw new InvalidValueException('Move this campus’s houses to another residence first.');
        }

        DB::transaction(function () use ($residence, $school, $actor): void {
            $residence->schools()->detach($school->id);

            $this->auditor->record(
                AuditAction::BoardingResidenceChanged,
                $residence,
                ['change' => 'campus_unlinked', 'school_id' => $school->id],
                $actor,
            );
        });
    }
}
