<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'note', 'issue_date', 'due_date', 'user_id'];

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
     * Get all of the feeInvoiceRecords for the FeeInvoice.
     *
     * @return HasMany<FeeInvoiceRecord, $this>
     */
    public function feeInvoiceRecords(): HasMany
    {
        return $this->hasMany(FeeInvoiceRecord::class);
    }

    /**
     * Limit the query to invoices for people in one school.
     *
     * The school link lives on the person's membership, not on a column.
     *
     * @param  Builder<FeeInvoice>  $query
     */
    public function scopeOfSchool(Builder $query, School|int|null $school = null): Builder
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return $query->whereHas('user', fn (Builder $user) => $user->ofSchool($schoolId));
    }

    public function scopeisDue(Builder $query): void
    {
        $query->whereHas('FeeInvoiceRecords', function ($query) {
            return $query->isDue();
        });
    }

    public function scopeisPaid(Builder $query): void
    {
        $query->whereDoesntHave('FeeInvoiceRecords', function ($query) {
            return $query->isDue();
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

    public function getPaidAttribute()
    {
        return $this->getSumOfFieldFromRecords('paid');
    }

    public function getWaiverAttribute()
    {
        return $this->getSumOfFieldFromRecords('waiver');
    }

    public function getFineAttribute()
    {
        return $this->getSumOfFieldFromRecords('fine');
    }

    public function getBalanceAttribute()
    {
        return $this->amount->plus($this->fine)
            ->minus($this->paid)
            ->minus($this->waiver);
    }
}
