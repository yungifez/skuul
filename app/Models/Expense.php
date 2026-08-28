<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class Expense extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id', 'financial_period_id', 'ledger_account_id',
        'ledger_transaction_id', 'amount', 'expense_date', 'description',
        'vendor', 'method', 'reference', 'note', 'program_id', 'fund',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'expense_date' => 'date:Y-m-d',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A recorded expense cannot be changed. Record a correction instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A recorded expense cannot be deleted. Record a correction instead.');
        });
    }

    /** @return BelongsTo<FinancialPeriod, $this> */
    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    /** @return BelongsTo<LedgerAccount, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

    /** @return BelongsTo<LedgerTransaction, $this> */
    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(LedgerTransaction::class);
    }

    /** @return BelongsTo<Program, $this> */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /** @return BelongsTo<User, $this> */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /** @param Builder<$this> $query */
    public function scopeInPeriod(Builder $query, int $periodId): Builder
    {
        return $query->where('financial_period_id', $periodId);
    }
}
