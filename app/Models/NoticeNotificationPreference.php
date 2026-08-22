<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\NoticeNotificationPreferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticeNotificationPreference extends Model
{
    /** @use HasFactory<NoticeNotificationPreferenceFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = ['user_id', 'school_id', 'email_enabled'];

    protected $casts = ['email_enabled' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
