<?php

namespace App\Models;

use App\Models\School;
use App\Models\StudentRecord;
use App\Models\TeacherRecord;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
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
        'school_id',
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

    //get first name
    public function firstName()
    {
        return explode(' ', $this->name)[0];
    }

    //get last name
    public function lastName()
    {
        return explode(' ', $this->name)[1];
    }

    //get other names
    public function otherNames()
    {
        $names = array_diff_key(explode(' ', $this->name), array_flip([0, 1]));

        return implode(' ', $names);
    }
}
