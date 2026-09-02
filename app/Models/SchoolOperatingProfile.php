<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\SchoolOperatingProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property array<string, string> $labels
 * @property Carbon|null $setup_completed_at
 */
class SchoolOperatingProfile extends Model
{
    /** @use HasFactory<SchoolOperatingProfileFactory> */
    use HasFactory;

    use InSchool;

    public const DEFAULT_PRESET = 'home_sections';

    public const PRESETS = [
        'home_sections' => ['academic_year' => 'Academic year', 'class_level' => 'Class', 'section' => 'Section', 'period' => 'Term', 'course' => 'Subject', 'fee' => 'School fees', 'homeroom_teacher' => 'Class teacher'],
        'subject_schedule' => ['academic_year' => 'Academic year', 'class_level' => 'Grade', 'section' => 'Homeroom', 'period' => 'Semester', 'course' => 'Course', 'fee' => 'Tuition', 'homeroom_teacher' => 'Class teacher'],
        'hybrid' => ['academic_year' => 'Academic year', 'class_level' => 'Grade', 'section' => 'Section', 'period' => 'Term', 'course' => 'Subject', 'fee' => 'Fees', 'homeroom_teacher' => 'Class teacher'],
    ];

    protected $fillable = ['school_id', 'preset', 'labels', 'setup_completed_at'];

    protected function casts(): array
    {
        return [
            'labels' => 'array',
            'setup_completed_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /** @return array<string, string> */
    public static function labelsFor(string $preset): array
    {
        return self::PRESETS[$preset] ?? self::PRESETS[self::DEFAULT_PRESET];
    }

    /**
     * Get the equal-sized starting language choices shown to school admins.
     *
     * @return array<string, array{title: string, description: string, labels: array<string, string>}>
     */
    public static function presetOptions(): array
    {
        return [
            'home_sections' => [
                'title' => 'Class-based school',
                'description' => 'Academic year · Class · Section · Term · Subject · School fees · Class teacher',
                'labels' => self::labelsFor('home_sections'),
            ],
            'subject_schedule' => [
                'title' => 'Grade and subject-based school',
                'description' => 'Academic year · Grade · Homeroom · Semester · Course · Tuition · Class teacher',
                'labels' => self::labelsFor('subject_schedule'),
            ],
            'hybrid' => [
                'title' => 'Mixed class and subject school',
                'description' => 'Academic year · Grade · Section · Term · Subject · Fees · Class teacher',
                'labels' => self::labelsFor('hybrid'),
            ],
        ];
    }
}
