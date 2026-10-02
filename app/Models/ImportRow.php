<?php

namespace App\Models;

use App\Enums\ImportRowState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One line of an imported file, what was wrong with it, and what it wrote.
 *
 * @property ImportRowState $state
 * @property array<string, mixed> $payload
 * @property array<int, string>|null $errors
 */
class ImportRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_batch_id',
        'line_number',
        'source_id',
        'payload',
        'state',
        'errors',
        'subject_type',
        'subject_id',
    ];

    /**
     * The default values for a new row.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'state' => ImportRowState::Pending->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'state' => ImportRowState::class,
        'payload' => 'array',
        'errors' => 'array',
        'line_number' => 'integer',
    ];

    /**
     * Limit the query to the rows that can be written.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeReady(Builder $query): Builder
    {
        return $query->where('state', ImportRowState::Valid);
    }

    /**
     * Limit the query to the rows that cannot be written.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeBroken(Builder $query): Builder
    {
        return $query->where('state', ImportRowState::Invalid);
    }

    /**
     * Get the import this row came from.
     *
     * @return BelongsTo<ImportBatch, $this>
     */
    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    /**
     * Get the record this row wrote.
     *
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
