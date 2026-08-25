<?php

namespace App\Models;

use App\Enums\LibraryReservationStatus;
use App\Traits\InSchool;
use Database\Factories\LibraryReservationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person's place in the queue for a title.
 *
 * The queue is the order the reservations were made in, and nothing else, so
 * nobody can be moved up it quietly.
 *
 * @property LibraryReservationStatus $status
 */
class LibraryReservation extends Model
{
    /** @use HasFactory<LibraryReservationFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'library_title_id',
        'user_id',
        'status',
        'reserved_on',
        'library_copy_id',
        'ready_on',
        'holds_until',
        'closed_on',
        'created_by',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => LibraryReservationStatus::Waiting->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => LibraryReservationStatus::class,
        'reserved_on' => 'date:Y-m-d',
        'ready_on' => 'date:Y-m-d',
        'holds_until' => 'date:Y-m-d',
        'closed_on' => 'date:Y-m-d',
    ];

    /**
     * Get the title that was asked for.
     *
     * @return BelongsTo<LibraryTitle, $this>
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(LibraryTitle::class, 'library_title_id');
    }

    /**
     * Get the person waiting.
     *
     * @return BelongsTo<User, $this>
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the copy being kept behind the desk, when there is one.
     *
     * @return BelongsTo<LibraryCopy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(LibraryCopy::class, 'library_copy_id');
    }

    /**
     * Limit the query to reservations that are still going.
     *
     * @param  Builder<self>  $query
     */
    public function scopeStillGoing(Builder $query): Builder
    {
        return $query->whereIn('status', [
            LibraryReservationStatus::Waiting->value,
            LibraryReservationStatus::Ready->value,
        ]);
    }

    /**
     * Limit the query to the queue of one title, in the order it was formed.
     *
     * @param  Builder<self>  $query
     */
    public function scopeQueueFor(Builder $query, LibraryTitle|int $title): Builder
    {
        return $query->where('library_title_id', $title instanceof LibraryTitle ? $title->id : $title)
            ->stillGoing()
            ->orderBy('id');
    }

    /**
     * Check whether this reservation is still going.
     */
    public function isOpen(): bool
    {
        return $this->status->isOpen();
    }

    /**
     * Get how many people are in front of this one.
     */
    public function placeInQueue(): int
    {
        return 1 + self::query()
            ->where('school_id', $this->school_id)
            ->queueFor($this->library_title_id)
            ->where('id', '<', $this->id)
            ->count();
    }

    /**
     * Check whether the hold has run out.
     */
    public function holdHasRunOut(): bool
    {
        return $this->status === LibraryReservationStatus::Ready
            && $this->holds_until !== null
            && $this->holds_until->isBefore(now()->startOfDay());
    }
}
