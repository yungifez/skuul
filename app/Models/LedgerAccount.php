<?php

namespace App\Models;

use App\Enums\LedgerAccountType;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One account in a school's chart of accounts.
 *
 * @property LedgerAccountType $type
 */
class LedgerAccount extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'type',
        'purpose',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'      => LedgerAccountType::class,
        'is_active' => 'boolean',
    ];

    /**
     * Limit the query to the account that serves one purpose.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeForPurpose(Builder $query, string $purpose): Builder
    {
        return $query->where('purpose', $purpose);
    }

    /**
     * Get the lines written against this account.
     *
     * @return HasMany<LedgerLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(LedgerLine::class);
    }

    /**
     * Get what the account holds now, on its own normal side.
     *
     * An asset with 500 debited and 200 credited holds 300.
     */
    public function balance(): float
    {
        $debit = (float) $this->lines()->sum('debit');
        $credit = (float) $this->lines()->sum('credit');

        return round($this->type->normalBalance() === 'debit' ? $debit - $credit : $credit - $debit, 2);
    }
}
