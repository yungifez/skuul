<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TimetableRecord extends Pivot
{
    use HasFactory;

    /**
     * Get the Subject that owns the TimeTableRecord.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
