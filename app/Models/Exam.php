<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'name',
        'description',
        'academic_period_id',
        'start_date',
        'stop_date',
        'active',
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
    ];

    /**
     * Get the academic period that owns the exam.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the slots of the exam.
     *
     * @return HasMany<ExamSlot, $this>
     */
    public function examSlots(): HasMany
    {
        return $this->hasMany(ExamSlot::class);
    }
}
