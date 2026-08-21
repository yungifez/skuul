<?php

namespace App\Models;

use App\Enums\IncidentParticipantRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person named in a case, and why they are named.
 *
 * @property IncidentParticipantRole $role
 */
class IncidentParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'student_record_id',
        'role',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => IncidentParticipantRole::class,
    ];

    /**
     * Get the case this person is named in.
     *
     * @return BelongsTo<Incident, $this>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the person.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the enrollment the case is about, when it names one.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }
}
