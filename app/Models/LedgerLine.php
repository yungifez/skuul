<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One side of a posted entry.
 *
 * @property float $debit
 * @property float $credit
 */
class LedgerLine extends Model
{
    use HasFactory;

    /**
     * A line is written with its entry and never touched again.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'ledger_transaction_id',
        'ledger_account_id',
        'debit',
        'credit',
        'memo',
        'student_record_id',
        'program_id',
        'fund',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
        'created_at' => 'datetime',
    ];

    /**
     * Keep the books append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A posted line cannot be changed. Post a reversal instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A posted line cannot be deleted. Post a reversal instead.');
        });
    }

    /**
     * Get the entry this line belongs to.
     *
     * @return BelongsTo<LedgerTransaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(LedgerTransaction::class, 'ledger_transaction_id');
    }

    /**
     * Get the account this line was written against.
     *
     * @return BelongsTo<LedgerAccount, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

    /**
     * Get the programme this line is counted against, when it names one.
     *
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the enrollment this line is about, when it names one.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }
}
