<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait InSchool
{
    /**
     * Scopes school procied else scopes school of currently authenticated user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeInSchool($query, ?School $school = null): Builder
    {
        $school == null ? $school = auth()->user()->school_id : $school->id;

        return $query->where('school_id', $school);
    }
}
