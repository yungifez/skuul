<?php

namespace App\Traits;

use App\Models\School;

trait InSchool
{
    /**
     * Scopes school procied else scopes school of currently authenticated user.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInSchool($query, ?School $school = null)
    {
        $school == null ? $school = auth()->user()->school_id : $school->id;

        return $query->where('school_id', $school);
    }
}
