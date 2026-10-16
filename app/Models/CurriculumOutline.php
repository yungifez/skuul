<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\CurriculumOutlineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A reusable scheme of work for a subject and class level, kept in the school's library.
 *
 * It belongs to no academic period, so it never freezes. Teachers copy its
 * topics into a syllabus draft.
 */
class CurriculumOutline extends Model
{
    /** @use HasFactory<CurriculumOutlineFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id', 'subject_id', 'academic_level_id', 'name', 'description', 'created_by',
    ];

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<AcademicLevel, $this>
     */
    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the topics in teaching order.
     *
     * @return HasMany<CurriculumOutlineTopic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(CurriculumOutlineTopic::class)
            ->orderByRaw('week is null')
            ->orderBy('week')
            ->orderBy('position')
            ->orderBy('id');
    }
}
