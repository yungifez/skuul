<?php

namespace App\Models;

use App\Enums\NoticeRecipientState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person's copy of a notice.
 *
 * Keeping a record per person is what lets the school answer "was the family
 * told?" instead of guessing from a publication date.
 *
 * @property NoticeRecipientState $state
 */
class NoticeRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_id',
        'user_id',
        'state',
        'delivered_at',
        'read_at',
        'dismissed_at',
        'failure_reason',
    ];

    /**
     * The default values for a new recipient record.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'state' => NoticeRecipientState::Pending->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'state' => NoticeRecipientState::class,
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    /**
     * Get the notice this copy belongs to.
     *
     * @return BelongsTo<Notice, $this>
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }

    /**
     * Get the person who received the notice.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record that the person opened the notice.
     */
    public function markRead(): self
    {
        if ($this->state === NoticeRecipientState::Read) {
            return $this;
        }

        $this->state = NoticeRecipientState::Read;
        $this->read_at = now();
        $this->save();

        return $this;
    }

    /**
     * Record that the person put the notice away.
     */
    public function dismiss(): self
    {
        $this->state = NoticeRecipientState::Dismissed;
        $this->dismissed_at = now();
        $this->save();

        return $this;
    }
}
