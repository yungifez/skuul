<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingResidence;
use App\Models\Dormitory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttachDormitoryToBoardingResidence
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Put one school-owned house inside a shared physical residence.
     *
     * The residence is locked, so a campus cannot be unlinked while its house
     * is being added.
     *
     * @throws InvalidValueException when the house cannot go in this residence
     */
    public function attach(BoardingResidence $residence, Dormitory $dormitory, ?User $actor = null): void
    {
        DB::transaction(function () use ($residence, $dormitory, $actor): void {
            $residence = BoardingResidence::query()->lockForUpdate()->findOrFail($residence->id);
            $dormitory = Dormitory::query()->lockForUpdate()->findOrFail($dormitory->id);
            $school = $dormitory->school()->firstOrFail();

            if ($school->organization_id !== $residence->organization_id) {
                throw new InvalidValueException('That house belongs to another organization.');
            }

            if (!$residence->schools()->whereKey($school->id)->exists()) {
                throw new InvalidValueException('Link the campus to this residence before adding its house.');
            }

            if ($dormitory->boarding_residence_id === $residence->id) {
                return;
            }

            if ($dormitory->boarding_residence_id !== null) {
                throw new InvalidValueException('Move this house out of its current residence first.');
            }

            $dormitory->update(['boarding_residence_id' => $residence->id]);

            $this->auditor->record(
                AuditAction::BoardingResidenceChanged,
                $residence,
                ['change' => 'house_attached', 'dormitory_id' => $dormitory->id, 'school_id' => $dormitory->school_id],
                $actor,
                $dormitory->school_id,
            );
        });
    }

    /**
     * Move a house out of a shared physical residence without deleting it.
     */
    public function detach(BoardingResidence $residence, Dormitory $dormitory, ?User $actor = null): void
    {
        DB::transaction(function () use ($residence, $dormitory, $actor): void {
            $dormitory = Dormitory::query()->lockForUpdate()->findOrFail($dormitory->id);

            if ($dormitory->boarding_residence_id !== $residence->id) {
                return;
            }

            $dormitory->update(['boarding_residence_id' => null]);

            $this->auditor->record(
                AuditAction::BoardingResidenceChanged,
                $residence,
                ['change' => 'house_detached', 'dormitory_id' => $dormitory->id, 'school_id' => $dormitory->school_id],
                $actor,
                $dormitory->school_id,
            );
        });
    }
}
