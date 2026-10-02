<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One recorded move of a case between states.
 */
class IncidentStatusChange extends Model
{
    use HasFactory;

    /**
     * A change is written once.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'incident_id',
        'from_status',
        'to_status',
        'reason',
        'changed_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_status' => IncidentStatus::class,
        'to_status' => IncidentStatus::class,
        'created_at' => 'datetime',
    ];

    /**
     * Keep the case history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Case history cannot be changed.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Case history cannot be deleted.');
        });
    }

    /**
     * Get the case this change belongs to.
     *
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the person who made the change.
     *
     * @return BelongsTo<User, $this>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
