<?php

namespace App\Console\Commands;

use App\Actions\Library\CloseReservation;
use App\Enums\LibraryReservationStatus;
use App\Models\LibraryReservation;
use Illuminate\Console\Command;

/**
 * Give up on holds nobody came for.
 *
 * A copy kept behind the desk for somebody who never comes is a copy nobody
 * can read. Once the hold has run out the reservation ends and the copy goes
 * to the next person in the queue, or back on the shelf.
 */
class ProcessLibraryHolds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:process-library-holds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End library holds nobody collected and pass the copy on';

    /**
     * Execute the console command.
     */
    public function handle(CloseReservation $close): int
    {
        $ended = 0;

        $reservations = LibraryReservation::query()
            ->where('status', LibraryReservationStatus::Ready->value)
            ->whereNotNull('holds_until')
            ->whereDate('holds_until', '<', now()->startOfDay())
            ->orderBy('id')
            ->lazyById();

        foreach ($reservations as $reservation) {
            $close->expire($reservation);
            $ended++;
        }

        $this->info("$ended holds ended.");

        return self::SUCCESS;
    }
}
