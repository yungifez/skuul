<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * The link between an outside identifier and the record it made here.
 *
 * This is what makes an import safe to run twice. The same identifier always
 * finds the same record, so a repeated file changes it instead of copying it.
 */
class ImportedRecord extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'type',
        'source_id',
        'subject_type',
        'subject_id',
    ];

    /**
     * Get the record the outside system points at.
     *
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
