<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Something the school decided to do about a case.
 */
class IncidentAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'type',
        'description',
        'due_on',
        'completed_at',
        'assigned_to',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_on'       => 'date:Y-m-d',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the case this action belongs to.
     *
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the person who has to do it.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if the action still has to be done.
     */
    public function isOutstanding(): bool
    {
        return $this->completed_at === null;
    }

    /**
     * Record that the action is done.
     */
    public function complete(): self
    {
        $this->completed_at ??= now();
        $this->save();

        return $this;
    }
}
