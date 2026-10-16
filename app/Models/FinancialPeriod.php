<?php

namespace App\Models;

use App\Enums\FinancialPeriodStatus;
use App\Traits\InSchool;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialPeriod extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'name',
        'starts_on',
        'ends_on',
        'status',
        'closed_at',
        'closed_by',
        'close_reason',
    ];

    protected $attributes = [
        'status' => FinancialPeriodStatus::Open->value,
    ];

    protected $casts = [
        'status' => FinancialPeriodStatus::class,
        'starts_on' => 'date:Y-m-d',
        'ends_on' => 'date:Y-m-d',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the school whose books this period closes.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user who closed the period.
     *
     * @return BelongsTo<User, $this>
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Get invoices posted in this period.
     *
     * @return HasMany<FeeInvoice, $this>
     */
    public function feeInvoices(): HasMany
    {
        return $this->hasMany(FeeInvoice::class);
    }

    /** @return HasMany<LedgerTransaction, $this> */
    public function ledgerTransactions(): HasMany
    {
        return $this->hasMany(LedgerTransaction::class);
    }

    /** @return HasMany<Expense, $this> */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /** @return HasMany<CashDeposit, $this> */
    public function cashDeposits(): HasMany
    {
        return $this->hasMany(CashDeposit::class);
    }

    /**
     * Limit the list to periods that accept new finance records.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', FinancialPeriodStatus::Open);
    }

    /**
     * Limit the list to periods that no longer accept new finance records.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', FinancialPeriodStatus::Closed);
    }

    public function coversDate(CarbonInterface|string $date): bool
    {
        $date = $date instanceof CarbonInterface ? $date : now()->parse($date);

        return $date->betweenIncluded($this->starts_on, $this->ends_on);
    }

    public function isOpen(): bool
    {
        return $this->status === FinancialPeriodStatus::Open;
    }

    public function isClosed(): bool
    {
        return $this->status === FinancialPeriodStatus::Closed;
    }
}
