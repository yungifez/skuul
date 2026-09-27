<?php

namespace App\Actions\Sharing;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\DataSharingStatus;
use App\Exceptions\InvalidValueException;
use App\Models\DataSharingRequest;
use App\Models\StudentRecord;
use App\Models\TransferPackage;
use App\Models\User;
use App\Services\Sharing\TransferPackageBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Hand the approved records over, and take them in at the other end.
 *
 * Approving a request does not move anything. This action is the separate
 * decision that builds the copy, and the receiving school still has to take
 * it in before it means anything there.
 */
class FulfilDataSharingRequest
{
    public function __construct(
        private TransferPackageBuilder $builder,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Build the package the request allows.
     *
     * @throws InvalidValueException when the request was not approved, has run out, or the learner moved campus
     */
    public function fulfil(DataSharingRequest $request, ?User $actor = null): TransferPackage
    {
        return DB::transaction(function () use ($request, $actor): TransferPackage {
            // A second click, or a revoke arriving at the same moment, waits
            // here and then sees the request as it now stands.
            $request = DataSharingRequest::query()->lockForUpdate()->findOrFail($request->id);

            if (!$request->status->allowsFulfilment()) {
                throw new InvalidValueException('Only an approved request can be handed over.');
            }

            if ($request->hasExpired()) {
                throw new InvalidValueException('This permission has run out.');
            }

            if (!$request->isStillHeldByTheAskedSchool()) {
                throw new InvalidValueException('The learner now attends another campus, so this school no longer holds their records. Decline this request; the other school must ask the learner\'s current campus.');
            }

            $package = TransferPackage::create([
                'data_sharing_request_id' => $request->id,
                'source_school_id' => $request->holding_school_id,
                'destination_school_id' => $request->requesting_school_id,
                'student_record_id' => $request->student_record_id,
                'categories' => $request->categories,
                'payload' => $this->builder->build($request),
                'built_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $request->status = DataSharingStatus::Fulfilled;
            $request->save();

            $this->auditor->record(
                AuditAction::TransferPackageBuilt,
                $package,
                ['categories' => $request->categories, 'destination_school_id' => $request->requesting_school_id],
                $actor,
                $request->holding_school_id,
            );

            return $package;
        });
    }

    /**
     * Take the package in at the school that asked for it.
     *
     * @throws InvalidValueException when the enrollment belongs to another school, it was taken in already,
     *                               or the holding school took the permission back
     */
    public function receive(TransferPackage $package, ?StudentRecord $enrollment = null, ?User $actor = null): TransferPackage
    {
        if ($enrollment !== null && $enrollment->school_id !== $package->destination_school_id) {
            throw new InvalidValueException('That enrollment is not in the school the package was sent to.');
        }

        return DB::transaction(function () use ($package, $enrollment, $actor): TransferPackage {
            $package = TransferPackage::query()->lockForUpdate()->findOrFail($package->id);

            if ($package->wasReceived()) {
                throw new InvalidValueException('This package was already taken in.');
            }

            // Taking the permission back after the hand-over still counts
            // until the records are taken in.
            $status = DataSharingRequest::query()->whereKey($package->data_sharing_request_id)->value('status');

            if ($status === DataSharingStatus::Revoked) {
                throw new InvalidValueException('The other school took this permission back. The records cannot be taken in.');
            }

            $package->forceFill([
                'received_at' => now(),
                'received_by' => $actor === null ? auth()->id() : $actor->id,
                'received_student_record_id' => $enrollment?->id,
            ])->save();

            $this->auditor->record(
                AuditAction::TransferPackageReceived,
                $package,
                ['source_school_id' => $package->source_school_id],
                $actor,
                $package->destination_school_id,
            );

            return $package;
        });
    }
}
