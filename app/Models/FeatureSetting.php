<?php

namespace App\Models;

use App\Enums\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Whether one feature is on for one school.
 *
 * @property Feature                   $feature
 * @property array<string, mixed>|null $config
 */
class FeatureSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'feature',
        'enabled',
        'config',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'feature' => Feature::class,
        'enabled' => 'boolean',
        'config'  => 'array',
    ];

    /**
     * Get the school this setting belongs to, when it names one.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
