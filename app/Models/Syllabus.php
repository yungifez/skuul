<?php

namespace App\Models;

use App\Enums\SyllabusStatus;
use App\Traits\InAcademicPeriod;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Syllabus extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'name', 'description', 'file', 'course_offering_id',
        'status', 'revision', 'revision_of_id', 'change_note', 'published_at', 'published_by',
    ];

    protected $attributes = [
        'status' => SyllabusStatus::Draft->value,
        'revision' => 1,
    ];

    protected $casts = [
        'status' => SyllabusStatus::class,
        'revision' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Get the published syllabus this revision replaces.
     *
     * @return BelongsTo<self, $this>
     */
    public function revisionOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'revision_of_id');
    }

    /**
     * Get the revisions started from this syllabus.
     *
     * @return HasMany<self, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(self::class, 'revision_of_id');
    }

    /**
     * Get the planned topics in teaching order.
     *
     * @return HasMany<SyllabusTopic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(SyllabusTopic::class)->orderByRaw('week is null')->orderBy('week')->orderBy('position')->orderBy('id');
    }

    /**
     * Get the draft revision that is still open, if one exists.
     */
    public function openRevision(): ?self
    {
        return $this->revisions()->where('status', SyllabusStatus::Draft)->first();
    }

    /**
     * Get the teaching week that holds a date, counted from the start of the period.
     *
     * Returns null when the period has no start date or does not cover the date.
     */
    public function teachingWeekOn(?CarbonInterface $date = null): ?int
    {
        $period = $this->courseOffering?->academicPeriod;
        $date = ($date ?? now())->copy()->startOfDay();

        if ($period?->starts_on === null || $date->lt($period->starts_on) || ($period->ends_on !== null && $date->gt($period->ends_on))) {
            return null;
        }

        return intdiv((int) $period->starts_on->diffInDays($date), 7) + 1;
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get the exact offering this syllabus supports.
     *
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Limit syllabi to one school through their course offering.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeInSchool(Builder $query, School|int|null $school = null): Builder
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return $query->whereHas('courseOffering', function (Builder $courseOfferings) use ($schoolId): void {
            $courseOfferings->where('school_id', $schoolId);
        });
    }

    /**
     * Get the academic period that freezes this syllabus.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->courseOffering?->academicPeriod;
    }
}
