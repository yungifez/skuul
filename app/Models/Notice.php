<?php

namespace App\Models;

use App\Enums\NoticeStatus;
use App\Traits\InSchool;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One message on the school board.
 *
 * @property NoticeStatus $status
 * @property array<string, mixed>|null $audience
 */
class Notice extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'title',
        'content',
        'attachment',
        'start_date',
        'stop_date',
        'active',
        'school_id',
        'status',
        'audience',
        'send_email',
        'scheduled_for',
        'published_at',
        'published_by',
        'revision',
        'revision_of_id',
    ];

    /**
     * The default values for a new notice.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => NoticeStatus::Draft->value,
        'revision' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => NoticeStatus::class,
        'audience' => 'array',
        'send_email' => 'boolean',
        'scheduled_for' => 'datetime',
        'published_at' => 'datetime',
        'revision' => 'integer',
    ];

    /**
     * Limit the query to notices the audience can read now.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', NoticeStatus::Published);
    }

    /**
     * Get the people this notice was sent to.
     *
     * @return HasMany<NoticeRecipient, $this>
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(NoticeRecipient::class);
    }

    /**
     * Get the person who published the notice.
     *
     * @return BelongsTo<User, $this>
     */
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get the notice this one revises.
     *
     * @return BelongsTo<Notice, $this>
     */
    public function revisionOf(): BelongsTo
    {
        return $this->belongsTo(Notice::class, 'revision_of_id');
    }

    /**
     * Get the newer drafts or published corrections of this notice.
     *
     * @return HasMany<Notice, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Notice::class, 'revision_of_id');
    }

    /**
     * Check if the audience can read the notice now.
     */
    public function isPublished(): bool
    {
        return $this->status === NoticeStatus::Published;
    }

    /**
     * Check if the notice is past its last day.
     */
    public function hasRunOut(): bool
    {
        $stopDate = $this->getAttribute('stop_date');

        return $stopDate !== null && Carbon::parse($stopDate)->endOfDay()->isPast();
    }

    public function scopeActive($query)
    {
        $query->where('start_date', '<=', date('Y-m-d'))
            ->where('stop_date', '>=', date('Y-m-d'))
            ->where('active', 1);
    }

    // used in view for displaying time on datatable
    public function getStartDateForHumansAttribute()
    {
        return Carbon::parse($this->start_date)->diffForHumans();
    }

    // used in view for displaying time on datatable
    public function getStopDateForHumansAttribute()
    {
        return Carbon::parse($this->stop_date)->diffForHumans();
    }
}
