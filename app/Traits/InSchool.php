<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait InSchool
{
    /**
     * Limit the query to records owned by one school.
     *
     * With no argument this uses the school of the current request. This is the
     * one place that turns "the school I am working in" into a query condition.
     *
     * @param  Builder  $query
     */
    public function scopeInSchool($query, School|int|null $school = null): Builder
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return $query->where($this->getTable().'.school_id', $schoolId);
    }
}
