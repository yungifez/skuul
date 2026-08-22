<?php

namespace App\Models;

use App\Enums\InstructionalModel;
use App\Traits\InSchool;
use Database\Factories\InstructionalModelSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The way one campus teaches one academic cycle.
 *
 * @property InstructionalModel $model
 * @property int                $school_id
 * @property int                $academic_year_id
 * @property int|null           $updated_by
 */
class InstructionalModelSetting extends Model
{
    /** @use HasFactory<InstructionalModelSettingFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'model',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'model' => InstructionalModel::class,
    ];

    /**
     * Get the campus this setting belongs to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the academic cycle this setting belongs to.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the person who last chose the model.
     *
     * @return BelongsTo<User, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
