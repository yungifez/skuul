<?php

namespace App\Models;

use App\Traits\InSchool;
use Brick\Money\Money as BrickMoney;
use Database\Factories\LibraryLendingRulesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * How long one campus lends for, and to how many people.
 *
 * A campus that has never set this still lends, on sensible defaults, because
 * nobody should have to fill in a settings form before issuing a first book.
 */
class LibraryLendingRules extends Model
{
    /** @use HasFactory<LibraryLendingRulesFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'loan_days',
        'learner_limit',
        'staff_limit',
        'renewals_allowed',
        'hold_days',
        'fine_per_day',
        'updated_by',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'loan_days' => 14,
        'learner_limit' => 3,
        'staff_limit' => 10,
        'renewals_allowed' => 1,
        'hold_days' => 3,
        'fine_per_day' => 0,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'loan_days' => 'integer',
        'learner_limit' => 'integer',
        'staff_limit' => 'integer',
        'renewals_allowed' => 'integer',
        'hold_days' => 'integer',
        'fine_per_day' => 'integer',
    ];

    /**
     * Get the rules one campus lends by, set or not.
     */
    public static function forSchool(School|int|null $school = null): self
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return self::query()->firstOrNew(['school_id' => $schoolId]);
    }

    /**
     * Get how many items this person may hold at once.
     */
    public function limitFor(User $borrower): int
    {
        return $borrower->studentRecord()->exists() ? $this->learner_limit : $this->staff_limit;
    }

    /**
     * Get what one day late costs.
     */
    public function dailyFine(): BrickMoney
    {
        return BrickMoney::ofMinor($this->fine_per_day, config('app.currency'));
    }

    /**
     * Check whether the campus charges for a late book at all.
     */
    public function chargesFines(): bool
    {
        return $this->fine_per_day > 0;
    }
}
