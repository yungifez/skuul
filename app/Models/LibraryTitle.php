<?php

namespace App\Models;

use Database\Factories\LibraryTitleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * What a book is, described once for the whole school group.
 *
 * A campus does not describe the same book again to lend its own copy, in the
 * same way that campuses share subjects.
 */
class LibraryTitle extends Model
{
    /** @use HasFactory<LibraryTitleFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'authors',
        'isbn',
        'category',
        'published_year',
        'summary',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_year' => 'integer',
    ];

    /**
     * Get the school group that keeps the catalogue.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get every copy any campus holds of this title.
     *
     * @return HasMany<LibraryCopy, $this>
     */
    public function copies(): HasMany
    {
        return $this->hasMany(LibraryCopy::class);
    }

    /**
     * Limit the query to the catalogue one campus can lend from.
     *
     * A title with no school group belongs to whoever added it, so a single
     * school that is not part of a group still has a catalogue.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForSchool(Builder $query, School|int|null $school = null): Builder
    {
        $school = $school instanceof School ? $school : School::find($school ?? current_school_id());

        return $query->where(function (Builder $catalogue) use ($school): void {
            $catalogue->whereNull('organization_id');

            if ($school?->organization_id !== null) {
                $catalogue->orWhere('organization_id', $school->organization_id);
            }
        });
    }
}
