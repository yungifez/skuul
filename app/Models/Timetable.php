<?php

namespace App\Models;

use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * One revision of a class or section schedule.
 *
 * @property TimetableStatus $status
 * @property int $revision
 * @property int|null $section_id
 * @property Carbon|null $published_at
 */
class Timetable extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $table = 'timetables';

    protected $fillable = [
        'name',
        'description',
        'status',
        'revision',
        'academic_period_id',
        'my_class_id',
        'section_id',
        'effective_from',
        'effective_to',
        'published_at',
        'published_by',
        'revision_of_id',
    ];

    /**
     * The default values for a new timetable.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => TimetableStatus::Draft->value,
        'revision' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TimetableStatus::class,
        'revision' => 'integer',
        'effective_from' => 'date:Y-m-d',
        'effective_to' => 'date:Y-m-d',
        'published_at' => 'datetime',
    ];

    /**
     * A published timetable stops changing.
     *
     * Only the state itself may still move, so the publish and archive
     * actions keep working. Everything else needs a new revision.
     */
    protected static function booted(): void
    {
        static::saving(function (self $timetable): void {
            if (!$timetable->exists || $timetable->getRawOriginal('status') !== TimetableStatus::Published->value) {
                return;
            }

            $allowed = ['status', 'published_at', 'published_by', 'updated_at'];

            if (array_diff($timetable->getDirtyKeys(), $allowed) !== []) {
                throw new InvalidValueException('This timetable is published. Create a revision to change it.');
            }
        });

        static::deleting(function (self $timetable): void {
            if ($timetable->status === TimetableStatus::Published) {
                throw new InvalidValueException('A published timetable cannot be deleted. Archive it instead.');
            }
        });
    }

    /**
     * Get the names of the attributes that changed.
     *
     * @return array<int, string>
     */
    public function getDirtyKeys(): array
    {
        return array_keys($this->getDirty());
    }

    /**
     * Limit the query to timetables in one state.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeWithStatus(Builder $query, TimetableStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Limit the query to the timetables the school teaches now.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', TimetableStatus::Published);
    }

    /**
     * Check if the timetable is in use.
     */
    public function isPublished(): bool
    {
        return $this->status === TimetableStatus::Published;
    }

    /**
     * Check if the entries of the timetable can still be changed.
     */
    public function acceptsChanges(): bool
    {
        return $this->status->acceptsChanges();
    }

    /**
     * Get the academic period the timetable belongs to.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the class the timetable belongs to.
     *
     * @return BelongsTo<MyClass, $this>
     */
    public function myClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * Get the section the timetable belongs to, when it names one.
     *
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the person who published the timetable.
     *
     * @return BelongsTo<User, $this>
     */
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get the revision this one replaced.
     *
     * @return BelongsTo<Timetable, $this>
     */
    public function revisionOf(): BelongsTo
    {
        return $this->belongsTo(Timetable::class, 'revision_of_id');
    }

    /**
     * Get the revisions that replaced this timetable.
     *
     * @return HasMany<Timetable, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Timetable::class, 'revision_of_id');
    }

    /**
     * Get the time slots of the timetable.
     *
     * @return HasMany<TimetableTimeSlot, $this>
     */
    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimetableTimeSlot::class, 'timetable_id');
    }
}
