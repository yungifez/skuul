<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RuntimeException;

/**
 * One posted entry in the books.
 *
 * A posted entry is never changed and never deleted. A mistake is corrected
 * by posting its reversal, so the books can always be read back.
 */
class LedgerTransaction extends Model
{
    use HasFactory;
    use InSchool;

    /**
     * The books only record when an entry was posted.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'school_id',
        'reference',
        'description',
        'transaction_date',
        'source_type',
        'source_id',
        'reversal_of_id',
        'posted_at',
        'posted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'date:Y-m-d',
        'posted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Keep the books append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A posted entry cannot be changed. Post a reversal instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A posted entry cannot be deleted. Post a reversal instead.');
        });
    }

    /**
     * Get the lines of the entry.
     *
     * @return HasMany<LedgerLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(LedgerLine::class);
    }

    /**
     * Get the record the entry came from, such as an invoice.
     *
     * @return MorphTo<Model, $this>
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the entry this one reverses.
     *
     * @return BelongsTo<LedgerTransaction, $this>
     */
    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(LedgerTransaction::class, 'reversal_of_id');
    }

    /**
     * Get the entry that reversed this one.
     *
     * @return HasMany<LedgerTransaction, $this>
     */
    public function reversals(): HasMany
    {
        return $this->hasMany(LedgerTransaction::class, 'reversal_of_id');
    }

    /**
     * Get the person who posted the entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Check if the entry was already corrected.
     */
    public function isReversed(): bool
    {
        return $this->reversals()->exists();
    }

    /**
     * Get the total of the debit side.
     */
    public function total(): float
    {
        return round((float) $this->lines()->sum('debit'), 2);
    }
}
