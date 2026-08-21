<?php

namespace App\Exceptions;

/**
 * Raised when a write lands in an academic period that is closed.
 */
class ClosedPeriodException extends ApplicationException {}
