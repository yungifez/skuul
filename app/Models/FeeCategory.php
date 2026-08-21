<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeCategory extends Model
{
    use HasFactory;
    use InSchool;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'school_id'];

    /**
     * Get the school that owns the FeeCategory.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all of the fees for the FeeCategory.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }
}
