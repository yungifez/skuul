<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\AssessmentTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentTemplate extends Model
{
    /** @use HasFactory<AssessmentTemplateFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = ['school_id', 'name', 'description', 'is_active', 'created_by'];

    protected $attributes = ['is_active' => true];

    protected $casts = ['is_active' => 'boolean'];

    /** @return HasMany<AssessmentTemplateCategory, $this> */
    public function categories(): HasMany
    {
        return $this->hasMany(AssessmentTemplateCategory::class)->orderBy('position')->orderBy('id');
    }

    /** @return HasMany<AssessmentTemplateItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(AssessmentTemplateItem::class)->orderBy('position')->orderBy('id');
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<AssessmentTemplateApplication, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(AssessmentTemplateApplication::class);
    }
}
