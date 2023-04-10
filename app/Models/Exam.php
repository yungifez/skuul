<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'semester_id',
        'start_date',
        'stop_date',
        'active',
        'publish_result',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'start_date'        => 'date:Y-m-d',
        'stop_date'         => 'date:Y-m-d',
        'active'            => 'boolean',
        'publish_result'    => 'boolean',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function examSlots(): HasMany
    {
        return $this->hasMany(ExamSlot::class);
    }
}
