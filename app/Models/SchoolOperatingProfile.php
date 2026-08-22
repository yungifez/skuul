<?php

namespace App\Models;

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

    public const PRESETS = [
        'home_sections' => ['class_level' => 'Class', 'section' => 'Arm', 'period' => 'Term', 'course' => 'Subject', 'fee' => 'School fees'],
        'subject_schedule' => ['class_level' => 'Grade', 'section' => 'Homeroom', 'period' => 'Semester', 'course' => 'Course', 'fee' => 'Tuition'],
        'hybrid' => ['class_level' => 'Grade', 'section' => 'Section', 'period' => 'Term', 'course' => 'Subject', 'fee' => 'Fees'],
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
