<?php

namespace App\Models;

use App\Traits\InSchool;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use SoftDeletes;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use InSchool;

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
     * @var array<int, string>
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
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday'          => 'datetime:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    /**
     * Active applicants.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeApplicants($query)
    {
        return $query->whereHas('accountApplication', function (Builder $query) {
            $query->otherCurrentStatus('rejected');
        })->role('applicant');
    }

    /**
     * Active applicants.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeRejectedApplicants($query)
    {
        return $query->role('applicant')->whereHas('accountApplication', function (Builder $query) {
            $query->currentStatus('rejected');
        });
    }

    public function scopeActiveStudents($query)
    {
        return $query->whereRelation('studentRecord', 'is_graduated', 0);
    }

    /**
     * Get the school that owns the User.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the studentRecord associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentRecord()
    {
        return $this->hasOne(StudentRecord::class);
    }

    /**
     * Get the studentRecord of graduation associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function graduatedStudentRecord()
    {
        return $this->hasOne(StudentRecord::class)->withoutGlobalScopes()->where('is_Graduated', true);
    }

    /**
     * Get the studentRecord of graduation associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function allStudentRecords()
    {
        return $this->hasOne(StudentRecord::class)->withoutGlobalScopes();
    }

    /**
     * The parents that belong to the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parents()
    {
        return $this->belongsToMany(ParentRecord::class);
    }

    /**
     * Get the teacherRecord associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacherRecord()
    {
        return $this->hasOne(TeacherRecord::class);
    }

    /**
     * Get the parent records associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parentRecord()
    {
        return $this->hasOne(ParentRecord::class);
    }

    /**
     * Get the AccountApplication associated with the User.
     */
    public function accountApplication(): HasOne
    {
        return $this->hasOne(AccountApplication::class);
    }

    //get first name
    public function firstName()
    {
        return explode(' ', $this->name)[0];
    }

    //get first name
    public function getFirstNameAttribute()
    {
        return $this->firstName();
    }

    //get last name
    public function lastName()
    {
        return explode(' ', $this->name)[1];
    }

    //get last name
    public function getLastNameAttribute()
    {
        return $this->lastName();
    }

    //get other names
    public function otherNames()
    {
        $names = array_diff_key(explode(' ', $this->name), array_flip([0, 1]));

        return implode(' ', $names);
    }

    //get other names
    public function getOtherNamesAttribute()
    {
        return $this->otherNames();
    }

    public function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        $email = trim($this->email);
        $email = strtolower($email);
        $email = md5($email);

        return 'https://www.gravatar.com/avatar/'.$email.'?d=https%3A%2F%2Fui-avatars.com%2Fapi%2F/'.urlencode($name).'/300/EBF4FF/7F9CF5';
    }

    //accessor for birthday

    public function getBirthdayAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * The subjects that belong to the User.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class);
    }

    public function adminlte_image()
    {
        return $this->defaultProfilePhotoUrl();
    }

    public function adminlte_desc()
    {
        $description = [];
        if ($this->school) {
            $description[] = $this->school->academicYear ? 'Academic year: '.$this->school->academicYear->name() : '';
            $description[] = $this->school->semester ? 'Semester: '.$this->school->semester->name : '';
        }

        return $descriptionString = implode(', ', $description);
    }

    public function adminlte_profile_url()
    {
        return 'profile/username';
    }
}
