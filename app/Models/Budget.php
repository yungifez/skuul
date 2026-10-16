<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\BudgetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * What one account is allowed over one stretch of the year.
 *
 * A budget is a plan, so it can be revised. What it is compared against comes
 * from the books, which cannot be.
 *
 * @property float $amount
 */
class Budget extends Model
{
    /** @use HasFactory<BudgetFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_period_id',
        'ledger_account_id',
        'program_id',
        'fund',
        'amount',
        'note',
        'scope_hash',
        'set_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * Keep the scope hash in step with what the budget is about.
     */
    protected static function booted(): void
    {
        static::saving(function (self $budget): void {
            $budget->scope_hash = self::hashFor(
                (int) $budget->academic_year_id,
                $budget->academic_period_id === null ? null : (int) $budget->academic_period_id,
                (int) $budget->ledger_account_id,
                $budget->program_id === null ? null : (int) $budget->program_id,
                $budget->fund,
            );
        });
    }

    /**
     * Work out the name for one plan, so the same plan cannot be written twice.
     */
    public static function hashFor(
        int $academicYearId,
        ?int $academicPeriodId,
        int $ledgerAccountId,
        ?int $programId,
        ?string $fund,
    ): string {
        return hash('sha256', implode(':', [
            $academicYearId,
            $academicPeriodId ?? 'year',
            $ledgerAccountId,
            $programId ?? 'all',
            $fund === null || $fund === '' ? 'all' : $fund,
        ]));
    }

    /**
     * Get the cycle the plan covers.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the term the plan covers, when it covers one term only.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the account the plan is about.
     *
     * @return BelongsTo<LedgerAccount, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

    /**
     * Get the programme the plan is narrowed to.
     *
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the person who last set the plan.
     *
     * @return BelongsTo<User, $this>
     */
    public function setBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Limit the query to the plans of one cycle.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForCycle(Builder $query, int $academicYearId): Builder
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Say in words what stretch of the year the plan covers.
     */
    public function coverage(): string
    {
        $period = $this->academicPeriod;

        return $period === null ? 'The whole year' : $period->name;
    }

    /**
     * Say in words how narrow the plan is.
     */
    public function narrowedTo(): string
    {
        $program = $this->program;

        $parts = array_filter([
            $program === null ? null : $program->name,
            $this->fund,
        ]);

        return $parts === [] ? 'Everything on this account' : implode(' · ', $parts);
    }
}
