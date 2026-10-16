<?php

namespace App\Exceptions;

/**
 * A visitor asked the public demo for a change it refuses.
 *
 * The demo is shared, so a change that would break it for the next visitor
 * is refused with a reason, and the visitor carries on.
 */
abstract class DemoChangeRefused extends ApplicationException {}
