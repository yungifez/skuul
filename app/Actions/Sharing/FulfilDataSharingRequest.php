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
     * @throws InvalidValueException when the request was not approved or has run out
     */
    public function fulfil(DataSharingRequest $request, ?User $actor = null): TransferPackage
    {
        if (!$request->status->allowsFulfilment()) {
            throw new InvalidValueException('Only an approved request can be handed over.');
        }

        if ($request->hasExpired()) {
            throw new InvalidValueException('This permission has run out.');
        }

        return DB::transaction(function () use ($request, $actor): TransferPackage {
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
     * @throws InvalidValueException when the enrollment belongs to another school or it was taken in already
     */
    public function receive(TransferPackage $package, ?StudentRecord $enrollment = null, ?User $actor = null): TransferPackage
    {
        if ($package->wasReceived()) {
            throw new InvalidValueException('This package was already taken in.');
        }

        if ($enrollment !== null && $enrollment->school_id !== $package->destination_school_id) {
            throw new InvalidValueException('That enrollment is not in the school the package was sent to.');
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
    }
}
