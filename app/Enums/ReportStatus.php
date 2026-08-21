<?php

namespace App\Enums;

/**
 * How far along a requested report is.
 */
enum ReportStatus: string
{
    /**
     * Waiting for a worker to pick it up.
     */
    case Queued = 'queued';

    /**
     * Being built now.
     */
    case Running = 'running';

    /**
     * Finished and waiting to be downloaded.
     */
    case Ready = 'ready';

    /**
     * It could not be built.
     */
    case Failed = 'failed';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Queued => 'Queued',
            self::Running => 'Building',
            self::Ready => 'Ready',
            self::Failed => 'Failed',
        };
    }
}
