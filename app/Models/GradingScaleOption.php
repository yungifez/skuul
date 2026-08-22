<?php

namespace App\Models;

use Database\Factories\GradingScaleOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingScaleOption extends Model
{
    /** @use HasFactory<GradingScaleOptionFactory> */
    use HasFactory;

    protected $fillable = [
        'grading_scale_id',
        'label',
        'points',
        'position',
    ];

    /**
     * The default values for a new option.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'position' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'float',
        'position' => 'integer',
    ];

    /**
     * Get the scale this option belongs to.
     *
     * @return BelongsTo<GradingScale, $this>
     */
    public function gradingScale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class);
    }
}
