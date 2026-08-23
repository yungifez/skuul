<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\DormitoryBedFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One bed a learner can be given.
 *
 * A bed is what makes capacity honest: a house holds as many boarders as it
 * has beds, and nobody has to keep a number up to date.
 */
class DormitoryBed extends Model
{
    /** @use HasFactory<DormitoryBedFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'dormitory_room_id',
        'name',
        'is_active',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the room the bed is in.
     *
     * @return BelongsTo<DormitoryRoom, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(DormitoryRoom::class, 'dormitory_room_id');
    }

    /**
     * Get every place ever given on this bed.
     *
     * @return HasMany<BoardingPlace, $this>
     */
    public function places(): HasMany
    {
        return $this->hasMany(BoardingPlace::class);
    }

    /**
     * Get the learner sleeping here now, if anybody is.
     */
    public function occupant(): ?StudentRecord
    {
        $place = BoardingPlace::query()
            ->current()
            ->where('dormitory_bed_id', $this->id)
            ->with('studentRecord.user')
            ->first();

        return $place?->studentRecord;
    }

    /**
     * Check whether somebody already sleeps here.
     */
    public function isTaken(): bool
    {
        return BoardingPlace::query()
            ->current()
            ->where('dormitory_bed_id', $this->id)
            ->exists();
    }

    /**
     * Limit the query to the beds still in use.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Limit the query to the beds nobody sleeps in.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->whereNotIn(
            'id',
            BoardingPlace::query()->current()->whereNotNull('dormitory_bed_id')->select('dormitory_bed_id'),
        );
    }
}
