<?php

namespace App\Actions\Facility;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\User;
use App\Services\Timetable\FacilityAvailability;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Claim a shared thing for a stretch of time.
 *
 * Two people booking the hall at once must not both succeed, so the thing is
 * locked while the booking is written and the clash check runs inside the
 * same transaction.
 */
class BookFacility
{
    public function __construct(
        private FacilityAvailability $availability,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Make the booking.
     *
     * @throws InvalidValueException when the times are wrong or something else has it
     */
    public function book(
        Facility $facility,
        Carbon $from,
        Carbon $to,
        string $purpose,
        ?User $actor = null,
    ): FacilityBooking {
        if ($to->lessThanOrEqualTo($from)) {
            throw new InvalidValueException('A booking has to end after it starts.');
        }

        if (trim($purpose) === '') {
            throw new InvalidValueException('Say what the booking is for.');
        }

        if (!$facility->is_active) {
            throw new InvalidValueException("$facility->name is out of use.");
        }

        return DB::transaction(function () use ($facility, $from, $to, $purpose, $actor): FacilityBooking {
            $facility = Facility::query()->lockForUpdate()->findOrFail($facility->getKey());

            $clashes = $this->availability->clashesFor($facility, $from, $to);

            if ($clashes !== []) {
                throw new InvalidValueException(implode(' ', $clashes));
            }

            $booking = FacilityBooking::create([
                'school_id' => $facility->school_id,
                'facility_id' => $facility->id,
                'starts_at' => $from,
                'ends_at' => $to,
                'purpose' => $purpose,
                'booked_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::FacilityBooked,
                $booking,
                [
                    'facility' => $facility->name,
                    'from' => $from->toDateTimeString(),
                    'to' => $to->toDateTimeString(),
                    'purpose' => $purpose,
                ],
                $actor,
                $facility->school_id,
            );

            return $booking;
        });
    }

    /**
     * Give the booking up again.
     *
     * @throws InvalidValueException when it was already cancelled
     */
    public function cancel(FacilityBooking $booking, string $reason, ?User $actor = null): FacilityBooking
    {
        if (!$booking->isRunning()) {
            throw new InvalidValueException('This booking was already given up.');
        }

        $booking->cancelled_at = now();
        $booking->cancelled_by = $actor === null ? auth()->id() : $actor->id;
        $booking->cancelled_reason = trim($reason) === '' ? null : $reason;
        $booking->save();

        $this->auditor->record(
            AuditAction::FacilityBookingCancelled,
            $booking,
            ['reason' => $reason],
            $actor,
            $booking->school_id,
        );

        return $booking;
    }
}
