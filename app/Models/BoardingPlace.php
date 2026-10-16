<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\BoardingPlaceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Where one learner slept, from a date.
 *
 * The record is written once. Moving a learner means writing the next place,
 * so a house can always answer where a child slept last term and who moved
 * them. A place with no bed means the learner stopped boarding.
 */
class BoardingPlace extends Model
{
    /** @use HasFactory<BoardingPlaceFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'dormitory_bed_id',
        'academic_year_id',
        'effective_on',
        'reason',
        'changed_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'effective_on' => 'date:Y-m-d',
    ];

    /**
     * Keep the history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Boarding history cannot be changed. Record the next place instead.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Boarding history cannot be deleted. Record the next place instead.');
        });
    }

    /**
     * Get the enrollment the place belongs to.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the bed the learner was given, when they were given one.
     *
     * @return BelongsTo<DormitoryBed, $this>
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(DormitoryBed::class, 'dormitory_bed_id');
    }

    /**
     * Get the cycle the place was given for.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the person who moved the learner.
     *
     * @return BelongsTo<User, $this>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Limit the query to where each learner sleeps now.
     *
     * The newest record for a learner is the true one, so the older ones are
     * left out rather than deleted.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereIn('id', function ($newest): void {
            $newest->from('boarding_places')
                ->selectRaw('max(id)')
                ->groupBy('student_record_id');
        });
    }

    /**
     * Get where one learner sleeps now.
     */
    public static function currentFor(StudentRecord $enrollment): ?self
    {
        return self::query()
            ->where('student_record_id', $enrollment->id)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Check whether the learner is boarding now.
     */
    public function isBoarding(): bool
    {
        return $this->dormitory_bed_id !== null;
    }

    /**
     * Count how many learners sleep in one house tonight.
     */
    public static function countInDormitory(int $dormitoryId): int
    {
        return (int) self::query()
            ->current()
            ->whereNotNull('dormitory_bed_id')
            ->whereIn('dormitory_bed_id', DB::table('dormitory_beds')
                ->join('dormitory_rooms', 'dormitory_rooms.id', '=', 'dormitory_beds.dormitory_room_id')
                ->where('dormitory_rooms.dormitory_id', $dormitoryId)
                ->select('dormitory_beds.id'))
            ->count();
    }
}
