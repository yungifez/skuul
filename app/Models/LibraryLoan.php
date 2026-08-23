<?php

namespace App\Models;

use App\Traits\InSchool;
use Brick\Money\Money as BrickMoney;
use Database\Factories\LibraryLoanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One time a copy left the library.
 *
 * A loan is written when the copy goes out and closed when it comes back. It
 * is never deleted, so the library can always say who had a book and when.
 * What changed in between is in the audit log.
 */
class LibraryLoan extends Model
{
    /** @use HasFactory<LibraryLoanFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'library_copy_id',
        'user_id',
        'issued_on',
        'due_on',
        'renewals',
        'returned_on',
        'fine_charged',
        'issued_by',
        'received_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'issued_on' => 'date:Y-m-d',
        'due_on' => 'date:Y-m-d',
        'returned_on' => 'date:Y-m-d',
        'renewals' => 'integer',
        'fine_charged' => 'integer',
    ];

    /**
     * Get the copy that went out.
     *
     * @return BelongsTo<LibraryCopy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(LibraryCopy::class, 'library_copy_id');
    }

    /**
     * Get the person who has the copy.
     *
     * @return BelongsTo<User, $this>
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the person who handed the copy over.
     *
     * @return BelongsTo<User, $this>
     */
    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the person who took the copy back.
     *
     * @return BelongsTo<User, $this>
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Check whether the copy is still out.
     */
    public function isOpen(): bool
    {
        return $this->returned_on === null;
    }

    /**
     * Get how many days late the copy is, or was.
     */
    public function daysLate(?string $asAt = null): int
    {
        $end = ($this->returned_on ?? now()->parse($asAt ?? now()->toDateString()))->copy()->startOfDay();
        $due = $this->due_on->copy()->startOfDay();

        return $due->greaterThanOrEqualTo($end) ? 0 : (int) $due->diffInDays($end);
    }

    /**
     * Get the fine the library charged for this loan.
     */
    public function fine(): BrickMoney
    {
        return BrickMoney::ofMinor($this->fine_charged, config('app.currency'));
    }

    /**
     * Limit the query to the copies still out.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('returned_on');
    }

    /**
     * Limit the query to the copies that should already be back.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOverdue(Builder $query, ?string $asAt = null): Builder
    {
        return $query->whereNull('returned_on')->whereDate('due_on', '<', $asAt ?? now()->toDateString());
    }
}
