<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\SchoolOperatingProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<string, string> $labels
 */
class SchoolOperatingProfile extends Model
{
    /** @use HasFactory<SchoolOperatingProfileFactory> */
    use HasFactory;

    use InSchool;

    public const PRESETS = [
        'home_sections' => ['academic_year' => 'Academic year', 'class_level' => 'Class', 'section' => 'Section', 'period' => 'Term', 'course' => 'Subject', 'fee' => 'School fees', 'homeroom_teacher' => 'Class teacher'],
        'subject_schedule' => ['academic_year' => 'Academic year', 'class_level' => 'Grade', 'section' => 'Homeroom', 'period' => 'Semester', 'course' => 'Course', 'fee' => 'Tuition', 'homeroom_teacher' => 'Class teacher'],
        'hybrid' => ['academic_year' => 'Academic year', 'class_level' => 'Grade', 'section' => 'Section', 'period' => 'Term', 'course' => 'Subject', 'fee' => 'Fees', 'homeroom_teacher' => 'Class teacher'],
    ];

    protected $fillable = ['school_id', 'preset', 'labels'];

    protected function casts(): array
    {
        return ['labels' => 'array'];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /** @return array<string, string> */
    public static function labelsFor(string $preset): array
    {
        return self::PRESETS[$preset] ?? self::PRESETS['home_sections'];
    }
}
