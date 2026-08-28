<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class CashDeposit extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id', 'financial_period_id', 'ledger_transaction_id', 'amount',
        'deposit_date', 'bank_reference', 'note', 'recorded_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'deposit_date' => 'date:Y-m-d',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A cash deposit cannot be changed. Record a correction instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A cash deposit cannot be deleted. Record a correction instead.');
        });
    }

    /** @return BelongsTo<FinancialPeriod, $this> */
    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    /** @return BelongsTo<LedgerTransaction, $this> */
    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(LedgerTransaction::class);
    }

    /** @return BelongsTo<User, $this> */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
