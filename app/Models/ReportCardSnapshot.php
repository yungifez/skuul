<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\ReportCardSnapshotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class ReportCardSnapshot extends Model
{
    /** @use HasFactory<ReportCardSnapshotFactory> */
    use HasFactory;

    use InSchool;

    public const UPDATED_AT = null;

    protected $fillable = ['school_id', 'student_record_id', 'academic_year_id', 'academic_period_id', 'revision', 'average_percentage', 'payload', 'reason', 'published_at', 'published_by'];

    protected $casts = ['revision' => 'integer', 'average_percentage' => 'float', 'payload' => 'array', 'published_at' => 'datetime', 'created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A published report card cannot be changed. Publish a new revision instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A published report card cannot be deleted.');
        });
    }

    /** @return BelongsTo<StudentRecord, $this> */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /** @return BelongsTo<AcademicYear, $this> */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /** @return BelongsTo<AcademicPeriod, $this> */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /** @return BelongsTo<User, $this> */
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
