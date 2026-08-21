<?php

namespace App\Models;

use App\Enums\ImportStatus;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One file the school imported, and what it did.
 *
 * @property ImportStatus $status
 */
class ImportBatch extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'type',
        'status',
        'source_name',
        'row_count',
        'valid_count',
        'invalid_count',
        'applied_count',
        'applied_at',
        'created_by',
    ];

    /**
     * The default values for a new import.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ImportStatus::Draft->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ImportStatus::class,
        'applied_at' => 'datetime',
        'row_count' => 'integer',
        'valid_count' => 'integer',
        'invalid_count' => 'integer',
        'applied_count' => 'integer',
    ];

    /**
     * Get the rows read from the file.
     *
     * @return HasMany<ImportRow, $this>
     */
    public function rows(): HasMany
    {
        return $this->hasMany(ImportRow::class)->orderBy('line_number');
    }

    /**
     * Get the person who started the import.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
