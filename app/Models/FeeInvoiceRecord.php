<?php

namespace App\Models;

use App\Casts\Money;
use Brick\Money\Money as BrickMoney;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One fee on one invoice.
 *
 * What has been paid against this line is not a column. It is the sum of the
 * allocations written against it, so two people taking money at once can
 * never leave the line saying something the payments do not.
 *
 * @property BrickMoney $amount
 * @property BrickMoney $fine
 * @property BrickMoney $waiver
 */
class FeeInvoiceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'waiver', 'fine', 'fee_id', 'fee_invoice_id'];

    protected $casts = [
        'amount' => Money::class,
        'fine' => Money::class,
        'waiver' => Money::class,
    ];

    /**
     * The sum of allocations against a line, in minor units.
     *
     * Written once so the scopes and the accessor cannot drift apart.
     */
    private const PAID_SUBQUERY = '(select coalesce(sum(payment_allocations.amount), 0) from payment_allocations where payment_allocations.fee_invoice_record_id = fee_invoice_records.id)';

    /**
     * Limit the query to lines that still owe money.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeIsDue(Builder $query): void
    {
        $query->whereRaw('(amount + fine - waiver) > '.self::PAID_SUBQUERY);
    }

    /**
     * Limit the query to lines that owe nothing.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeIsPaid(Builder $query): void
    {
        $query->whereRaw('(amount + fine - waiver) <= '.self::PAID_SUBQUERY);
    }

    /**
     * Get the parts of payments written against this line.
     *
     * @return HasMany<PaymentAllocation, $this>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    /**
     * Get what has been paid against this line.
     */
    public function getPaidAttribute(): BrickMoney
    {
        $minor = $this->relationLoaded('allocations')
            ? $this->allocations->sum(fn (PaymentAllocation $allocation): int => $allocation->amount->getMinorAmount()->toInt())
            : (int) $this->allocations()->sum('amount');

        return BrickMoney::ofMinor($minor, config('app.currency'));
    }

    /**
     * Get what the line asks for, after any waiver and fine.
     */
    public function getPayableAttribute(): BrickMoney
    {
        return $this->amount->plus($this->fine)->minus($this->waiver);
    }

    /**
     * Get what is still owed on this line.
     */
    public function getOutstandingAttribute(): BrickMoney
    {
        $outstanding = $this->payable->minus($this->paid);

        return $outstanding->isNegative()
            ? BrickMoney::zero(config('app.currency'))
            : $outstanding;
    }

    /**
     * Get the fee that owns the FeeInvoice.
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get the feeInvoice that owns the FeeInvoiceRecord.
     */
    public function feeInvoice(): BelongsTo
    {
        return $this->belongsTo(FeeInvoice::class);
    }
}
