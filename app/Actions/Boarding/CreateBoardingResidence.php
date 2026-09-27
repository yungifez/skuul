<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingResidence;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateBoardingResidence
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Create an organization-owned physical residence.
     *
     * @throws InvalidValueException when the organization already has a residence with that name
     */
    public function create(
        Organization $organization,
        string $name,
        ?string $notes = null,
        ?User $actor = null,
    ): BoardingResidence {
        $name = trim($name);
        $notes = $notes === null || trim($notes) === '' ? null : trim($notes);

        return DB::transaction(function () use ($organization, $name, $notes, $actor): BoardingResidence {
            Organization::query()->lockForUpdate()->findOrFail($organization->id);

            if ($organization->boardingResidences()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists()) {
                throw new InvalidValueException('This organization already has a residence with that name.');
            }

            $residence = BoardingResidence::create([
                'organization_id' => $organization->id,
                'name' => $name,
                'notes' => $notes,
            ]);

            $this->auditor->record(
                AuditAction::BoardingResidenceChanged,
                $residence,
                ['change' => 'created', 'organization_id' => $organization->id],
                $actor,
            );

            return $residence;
        });
    }
}
