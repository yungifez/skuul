<?php

use App\Models\School;
use App\Services\School\SchoolContext;

if (!function_exists('school_context')) {
    /**
     * Get the school context for the current request.
     */
    function school_context(): SchoolContext
    {
        return app(SchoolContext::class);
    }
}

if (!function_exists('current_school')) {
    /**
     * Get the school the current request works in.
     */
    function current_school(): ?School
    {
        return school_context()->school();
    }
}

if (!function_exists('current_school_id')) {
    /**
     * Get the id of the school the current request works in.
     */
    function current_school_id(): ?int
    {
        return school_context()->id();
    }
}
