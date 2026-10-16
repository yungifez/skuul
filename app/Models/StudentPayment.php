<?php

namespace App\Models;

use App\Casts\Money;
use App\Contracts\PaymentChannel;
use App\Exceptions\InvalidValueException;
use App\Services\Finance\PaymentChannelRegistry;
use App\Traits\InSchool;
use Brick\Money\Money as BrickMoney;
use Database\Factories\StudentPaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

/**
 * Money the school received from one student or guardian.
 *
 * A payment is never edited. Taking one back means recording its reversal, so
 * a receipt handed to a family always matches what the school holds.
 *
 * @property string $method
 * @property BrickMoney $amount
 */
class StudentPayment extends Model
{
    /** @use HasFactory<StudentPaymentFactory> */
    use HasFactory;

    use InSchool;

    /**
     * A payment is written once and never touched again.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'financial_period_id',
        'amount',
        'method',
        'reference',
        'received_on',
        'note',
        'ledger_transaction_id',
        'reversal_of_id',
        'recorded_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => Money::class,
        'received_on' => 'date:Y-m-d',
        'created_at' => 'datetime',
    ];

    /**
     * Keep the payment record append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A payment cannot be changed. Record its reversal instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A payment cannot be deleted. Record its reversal instead.');
        });
    }

    /**
     * Get the way the money reached the school.
     *
     * The channel is looked up rather than cast, so a school can add a way to
     * pay without a change to this model or to the payments already stored.
     */
    public function channel(): PaymentChannel
    {
        return app(PaymentChannelRegistry::class)->get($this->method);
    }

    /**
     * Get the label of the way the money reached the school.
     */
    public function methodLabel(): string
    {
        try {
            return $this->channel()->label();
        } catch (InvalidValueException) {
            return $this->method;
        }
    }

    /**
     * Get the enrollment the money was taken for.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the financial period in which this payment was received.
     *
     * @return BelongsTo<FinancialPeriod, $this>
     */
    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    /**
     * Get the invoice lines this payment settled.
     *
     * @return HasMany<PaymentAllocation, $this>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    /**
     * Get the books entry this payment produced.
     *
     * @return BelongsTo<LedgerTransaction, $this>
     */
    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(LedgerTransaction::class);
    }

    /**
     * Get the payment this one takes back.
     *
     * @return BelongsTo<StudentPayment, $this>
     */
    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(StudentPayment::class, 'reversal_of_id');
    }

    /**
     * Get the reversal of this payment, when there is one.
     *
     * @return HasMany<StudentPayment, $this>
     */
    public function reversals(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'reversal_of_id');
    }

    /**
     * Get the person who took the money.
     *
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get what this payment has already settled.
     */
    public function allocated(): BrickMoney
    {
        return BrickMoney::ofMinor(
            (int) $this->allocations()->sum('amount'),
            config('app.currency'),
        );
    }

    /**
     * Get the part of this payment no invoice has used yet.
     *
     * This is the credit the school holds for the family.
     */
    public function unallocated(): BrickMoney
    {
        return $this->amount->minus($this->allocated());
    }

    /**
     * Check whether the payment was already taken back.
     */
    public function isReversed(): bool
    {
        return $this->reversals()->exists();
    }

    /**
     * Check whether this record is itself a reversal.
     */
    public function isReversal(): bool
    {
        return $this->reversal_of_id !== null;
    }

    /**
     * Limit the query to payments that still count.
     *
     * A reversal and the payment it took back cancel each other, so neither
     * one is money the school holds.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeStillStanding(Builder $query): Builder
    {
        return $query
            ->whereNull('reversal_of_id')
            ->whereDoesntHave('reversals');
    }

    /**
     * Limit the query to payments that still hold unused money.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeWithCreditLeft(Builder $query): Builder
    {
        return $query
            ->stillStanding()
            ->whereRaw('amount > coalesce((select sum(amount) from payment_allocations where payment_allocations.student_payment_id = student_payments.id), 0)');
    }
}
