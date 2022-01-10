<?php

namespace App\Models;

use App\Models\School;
use App\Scopes\SchoolScope;
use App\Models\StudentRecord;
use App\Models\TeacherRecord;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'address',
        'blood_group',
        'religion',
        'nationality',
        'phone',
        'state',
        'city',
        'gender',
        'school_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the school that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the studentRecord associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentRecord()
    {
        return $this->hasOne(StudentRecord::class);
    }

    /**
     * Get the teacherRecord associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacherRecord()
    {
        return $this->hasOne(TeacherRecord::class);
    }
}