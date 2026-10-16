<?php

namespace App\Models;

use Database\Factories\CurriculumOutlineTopicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One planned topic of a library outline.
 */
class CurriculumOutlineTopic extends Model
{
    /** @use HasFactory<CurriculumOutlineTopicFactory> */
    use HasFactory;

    protected $fillable = [
        'curriculum_outline_id', 'week', 'position', 'title', 'objectives', 'content', 'resources',
    ];

    protected $casts = [
        'week' => 'integer',
        'position' => 'integer',
    ];

    /**
     * @return BelongsTo<CurriculumOutline, $this>
     */
    public function outline(): BelongsTo
    {
        return $this->belongsTo(CurriculumOutline::class, 'curriculum_outline_id');
    }
}
