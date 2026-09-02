<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One request to build a report.
 *
 * @property int|null $academic_period_id
 * @property ReportStatus $status
 * @property array<string, mixed>|null $parameters
 */
class ReportRun extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'type',
        'format',
        'status',
        'parameters',
        'academic_year_id',
        'academic_period_id',
        'financial_period_id',
        'file_path',
        'row_count',
        'error',
        'requested_by',
        'started_at',
        'completed_at',
    ];

    /**
     * The default values for a new request.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ReportStatus::Queued->value,
        'format' => 'csv',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ReportStatus::class,
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the person who asked for the report.
     *
     * @return BelongsTo<User, $this>
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /** @return BelongsTo<FinancialPeriod, $this> */
    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    /**
     * Check if the file is waiting to be downloaded.
     */
    public function isReady(): bool
    {
        return $this->status === ReportStatus::Ready && $this->file_path !== null;
    }
}
