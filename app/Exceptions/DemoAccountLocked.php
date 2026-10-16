<?php

namespace App\Exceptions;

/**
 * A visitor tried to change how a demo account signs in.
 *
 * Every visitor signs in with the same accounts. A new password, email,
 * two-factor setting or account status would lock the next visitor out
 * until the hourly reset, so the demo keeps them as they are.
 */
class DemoAccountLocked extends DemoChangeRefused
{
    public function __construct()
    {
        parent::__construct('The demo accounts keep their email, password, two-factor sign-in and status, so every visitor can sign in. Every other change works.');
    }
}
