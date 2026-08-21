<?php

use App\Enums\Feature;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Semester;
use App\Services\Academic\AcademicPeriodContext;
use App\Services\Feature\FeatureManager;
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

if (!function_exists('academic_period_context')) {
    /**
     * Get the academic period context for the current request.
     */
    function academic_period_context(): AcademicPeriodContext
    {
        return app(AcademicPeriodContext::class);
    }
}

if (!function_exists('current_academic_year')) {
    /**
     * Get the academic year the current request works in.
     */
    function current_academic_year(): ?AcademicYear
    {
        return academic_period_context()->academicYear();
    }
}

if (!function_exists('current_academic_year_id')) {
    /**
     * Get the id of the academic year the current request works in.
     */
    function current_academic_year_id(): ?int
    {
        return academic_period_context()->academicYearId();
    }
}

if (!function_exists('current_semester')) {
    /**
     * Get the semester the current request works in.
     */
    function current_semester(): ?Semester
    {
        return academic_period_context()->semester();
    }
}

if (!function_exists('current_semester_id')) {
    /**
     * Get the id of the semester the current request works in.
     */
    function current_semester_id(): ?int
    {
        return academic_period_context()->semesterId();
    }
}

if (!function_exists('features')) {
    /**
     * Get the service that answers which features are on.
     */
    function features(): FeatureManager
    {
        return app(FeatureManager::class);
    }
}

if (!function_exists('feature_enabled')) {
    /**
     * Check if a feature is on in the school being worked in.
     */
    function feature_enabled(Feature $feature): bool
    {
        return features()->enabled($feature);
    }
}
