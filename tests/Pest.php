<?php

/*
 * Pest runs this project's PHPUnit test classes, and nothing here configures
 * it further.
 *
 * Do not turn on test impact analysis with `pest()->tia()->locally()`. TIA
 * refuses to run a PHPUnit class, and every test in this project is one, so
 * the whole suite stops on the first file it reads:
 *
 *   ERROR  Tia mode requires Pest tests.
 *
 * The project writes PHPUnit classes on purpose. See .ai/rules/tests.md.
 */
