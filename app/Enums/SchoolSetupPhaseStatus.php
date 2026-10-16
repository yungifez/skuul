<?php

namespace App\Enums;

/**
 * The lifecycle of a school setup phase.
 */
enum SchoolSetupPhaseStatus: string
{
    /**
     * Required setup is still incomplete.
     */
    case InProgress = 'in_progress';

    /**
     * Required setup is complete and the school can start daily work.
     */
    case Ready = 'ready';

    /**
     * The readiness notice was acknowledged by a school administrator.
     */
    case Acknowledged = 'acknowledged';
}
