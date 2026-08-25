<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * One dated, append-only note attached to a case.
 */
class IncidentNote extends Model
{
    use HasFactory;
    use InSchool;

    /**
     * A note is written once.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'school_id',
        'incident_id',
        'body',
        'is_restricted',
        'written_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_restricted' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Keep the note history append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('A case note cannot be changed.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('A case note cannot be deleted.');
        });
    }

    /**
     * Get the case this note belongs to.
     *
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the person who wrote the note.
     *
     * @return BelongsTo<User, $this>
     */
    public function writtenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'written_by');
    }
}
