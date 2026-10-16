<?php

namespace App\Models;

use App\Casts\Money;
use Brick\Money\Money as BrickMoney;
use Database\Factories\PaymentAllocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * The part of one payment that settled one invoice line.
 *
 * Splitting a payment across lines is how one receipt can cover several
 * invoices, and how the office can say which fee a family has cleared.
 *
 * @property BrickMoney $amount
 */
class PaymentAllocation extends Model
{
    /** @use HasFactory<PaymentAllocationFactory> */
    use HasFactory;

    /**
     * An allocation is written with its payment and never touched again.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'student_payment_id',
        'fee_invoice_id',
        'fee_invoice_record_id',
        'amount',
        'reversal_of_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => Money::class,
        'created_at' => 'datetime',
    ];

    /**
     * Keep the allocation append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('An allocation cannot be changed. Record its reversal instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('An allocation cannot be deleted. Record its reversal instead.');
        });
    }

    /**
     * Get the payment this allocation came out of.
     *
     * @return BelongsTo<StudentPayment, $this>
     */
    public function studentPayment(): BelongsTo
    {
        return $this->belongsTo(StudentPayment::class);
    }

    /**
     * Get the invoice this allocation settled part of.
     *
     * @return BelongsTo<FeeInvoice, $this>
     */
    public function feeInvoice(): BelongsTo
    {
        return $this->belongsTo(FeeInvoice::class);
    }

    /**
     * Get the invoice line this allocation settled part of.
     *
     * @return BelongsTo<FeeInvoiceRecord, $this>
     */
    public function feeInvoiceRecord(): BelongsTo
    {
        return $this->belongsTo(FeeInvoiceRecord::class);
    }
}
