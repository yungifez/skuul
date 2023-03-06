<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeInvoiceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['paid', 'amount', 'waiver', 'fine', 'fee_id', 'fee_invoice_id'];

    protected $casts = [
        'amount' => Money::class,
        'fine'   => Money::class,
        'paid'   => Money::class,
        'waiver' => Money::class,
    ];

    public function scopeIsDue(Builder $query)
    {
        $query->whereRaw('(amount + fine) > ( paid + waiver) ');
    }

    public function scopeIsPaid(Builder $query)
    {
        $query->whereRaw('(amount + fine) <= ( paid + waiver) ');
    }

    /**
     * Get the fee that owns the FeeInvoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get the feeInvoice that owns the FeeInvoiceRecord.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feeInvoice(): BelongsTo
    {
        return $this->belongsTo(FeeInvoice::class);
    }
}
