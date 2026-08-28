<?php

namespace App\Models;

use App\Casts\Money;
use Brick\Money\Money as BrickMoney;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'note',
        'issue_date',
        'due_date',
        'user_id',
        'school_id',
        'student_record_id',
        'financial_period_id',
        'ledger_transaction_id',
    ];

    protected $casts = [
        'issue_date' => 'datetime:Y-m-d',
        'due_date' => 'datetime:Y-m-d',
        'amount' => Money::class,
        'fine' => Money::class,
        'paid' => Money::class,
        'waiver' => Money::class,
    ];

    /**
     * Get the user that owns the FeeInvoice.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the school whose books contain this invoice.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the enrollment this invoice is for.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the financial period in which this invoice was posted.
     *
     * @return BelongsTo<FinancialPeriod, $this>
     */
    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    /**
     * Get the ledger entry created for this invoice.
     *
     * @return BelongsTo<LedgerTransaction, $this>
     */
    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(LedgerTransaction::class);
    }

    /**
     * Get all of the feeInvoiceRecords for the FeeInvoice.
     *
     * @return HasMany<FeeInvoiceRecord, $this>
     */
    public function feeInvoiceRecords(): HasMany
    {
        return $this->hasMany(FeeInvoiceRecord::class);
    }

    /**
     * Limit the query to invoices in one school's books.
     *
     * @param  Builder<FeeInvoice>  $query
     */
    public function scopeOfSchool(Builder $query, School|int|null $school = null): Builder
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return $query->where('school_id', $schoolId);
    }

    public function scopeIsDue(Builder $query): Builder
    {
        return $query->whereHas('feeInvoiceRecords', function (Builder $query): void {
            $query->isDue();
        });
    }

    public function scopeIsPaid(Builder $query): Builder
    {
        return $query->whereDoesntHave('feeInvoiceRecords', function (Builder $query): void {
            $query->isDue();
        });
    }

    private function getSumOfFieldFromRecords($attribute)
    {
        $total = $this->loadMissing('feeInvoiceRecords')->feeInvoiceRecords->map(function ($model) {
            return $model->getAttributes();
        })->sum($attribute);

        return $this->castAttribute($attribute, $total);
    }

    public function getAmountAttribute()
    {
        return $this->getSumOfFieldFromRecords('amount');
    }

    /**
     * Get what has been paid against this invoice.
     *
     * The answer comes from the allocations written against the invoice, not
     * from a stored total, so it always agrees with the payments.
     */
    public function getPaidAttribute(): BrickMoney
    {
        $minor = $this->relationLoaded('allocations')
            ? $this->allocations->sum(fn (PaymentAllocation $allocation): int => $allocation->amount->getMinorAmount()->toInt())
            : (int) $this->allocations()->sum('amount');

        return BrickMoney::ofMinor($minor, config('app.currency'));
    }

    /**
     * Get the parts of payments written against this invoice.
     *
     * @return HasMany<PaymentAllocation, $this>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    /**
     * Get the payments that settled part of this invoice.
     *
     * @return BelongsToMany<StudentPayment, $this>
     */
    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(StudentPayment::class, 'payment_allocations', 'fee_invoice_id', 'student_payment_id')
            ->withPivot('amount')
            ->distinct();
    }

    /**
     * Check whether the invoice has been settled in full.
     */
    public function isSettled(): bool
    {
        return !$this->balance->isPositive();
    }

    public function getWaiverAttribute()
    {
        return $this->getSumOfFieldFromRecords('waiver');
    }

    public function getFineAttribute()
    {
        return $this->getSumOfFieldFromRecords('fine');
    }

    /**
     * Get what is still owed on this invoice.
     */
    public function getBalanceAttribute(): BrickMoney
    {
        return $this->amount->plus($this->fine)
            ->minus($this->paid)
            ->minus($this->waiver);
    }
}
