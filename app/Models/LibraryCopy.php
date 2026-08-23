<?php

namespace App\Models;

use App\Enums\LibraryCopyStatus;
use App\Traits\InSchool;
use Database\Factories\LibraryCopyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One object on one campus's shelf.
 *
 * Whether the copy is out is not stored here. It is answered by the loans, so
 * the shelf and the loan record cannot drift apart.
 *
 * @property LibraryCopyStatus $status
 */
class LibraryCopy extends Model
{
    /** @use HasFactory<LibraryCopyFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'library_title_id',
        'barcode',
        'status',
        'shelf_mark',
        'note',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => LibraryCopyStatus::OnShelf->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => LibraryCopyStatus::class,
    ];

    /**
     * Get what the copy is a copy of.
     *
     * @return BelongsTo<LibraryTitle, $this>
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(LibraryTitle::class, 'library_title_id');
    }

    /**
     * Get every time this copy went out.
     *
     * @return HasMany<LibraryLoan, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class);
    }

    /**
     * Get the loan this copy is out on, if it is out.
     */
    public function openLoan(): ?LibraryLoan
    {
        return $this->loans()->whereNull('returned_on')->latest('id')->first();
    }

    /**
     * Check whether somebody has the copy now.
     */
    public function isOut(): bool
    {
        return $this->loans()->whereNull('returned_on')->exists();
    }

    /**
     * Check whether the copy can go out to somebody today.
     */
    public function canBeLent(): bool
    {
        return $this->status->canBeLent() && !$this->isOut();
    }

    /**
     * Say in words where the copy is.
     */
    public function whereabouts(): string
    {
        if (!$this->status->isHeld()) {
            return $this->status->label();
        }

        return $this->isOut() ? 'Out on loan' : 'On the shelf';
    }

    /**
     * Limit the query to the copies nobody has out.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', LibraryCopyStatus::OnShelf)
            ->whereDoesntHave('loans', fn (Builder $loan) => $loan->whereNull('returned_on'));
    }
}
