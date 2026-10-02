<?php

namespace App\Exceptions;

/**
 * A visitor tried to delete a record in the public demo.
 *
 * Deleting would empty the demo for everyone until the next hourly reset,
 * so the demo refuses it and says why.
 */
class DemoDeletionRefused extends ApplicationException
{
    public function __construct()
    {
        parent::__construct('Deleting is turned off in the demo. Every other change works, and the demo resets every hour.');
    }
}
