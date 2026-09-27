<?php

namespace App\Actions\Facility;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Take a shared thing out of use, or bring it back.
 *
 * Bookings already over are the campus's history and stay as they are. A
 * booking still ahead cannot happen in something out of use, so it is given
 * up, and the person who made it can see why.
 */
class ManageFacility
{
    public function __construct(private BookFacility $bookFacility) {}

    /**
     * Take the thing out of use, and give up every booking still ahead.
     *
     * @return int how many bookings were given up
     */
    public function retire(Facility $facility, ?User $actor = null): int
    {
        return DB::transaction(function () use ($facility, $actor): int {
            $facility = Facility::query()->lockForUpdate()->findOrFail($facility->getKey());

            if (!$facility->is_active) {
                return 0;
            }

            $facility->is_active = false;
            $facility->save();

            $ahead = FacilityBooking::query()
                ->where('facility_id', $facility->id)
                ->running()
                ->where('ends_at', '>', now())
                ->get();

            foreach ($ahead as $booking) {
                $this->bookFacility->cancel($booking, "{$facility->name} was taken out of use.", $actor);
            }

            return $ahead->count();
        });
    }

    /**
     * Let the campus book it again.
     */
    public function restore(Facility $facility): Facility
    {
        $facility->is_active = true;
        $facility->save();

        return $facility;
    }
}
