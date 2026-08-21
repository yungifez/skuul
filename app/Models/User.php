<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Enums\SchoolMembershipStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

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
        'account_status'    => AccountStatus::class,
        'is_platform_admin' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected static function booted()
    {
        static::addGlobalScope('orderByName', function (Builder $builder) {
            $builder->orderBy('name');
        });
    }

    public function scopeStudents($query)
    {
        return $query->role(Role::Student);
    }

    /**
     * Limit the query to accounts that can sign in and use the application.
     *
     * @param Builder $query
     */
    public function scopeActiveAccounts($query): Builder
    {
        return $query->where('account_status', AccountStatus::Active);
    }

    public function scopeActiveStudents($query)
    {
        return $query->whereRelation('studentRecord', 'is_graduated', 0);
    }

    /**
     * Limit the query to people who can work in the given school.
     *
     * @param Builder $query
     */
    public function scopeOfSchool($query, School|int|null $school = null): Builder
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return $query->whereHas('schoolMemberships', function (Builder $membership) use ($schoolId): void {
            $membership->where('school_id', $schoolId)
                ->where('status', SchoolMembershipStatus::Active);
        });
    }

    /**
     * Get every school access record for this person.
     *
     * @return HasMany<SchoolMembership, $this>
     */
    public function schoolMemberships(): HasMany
    {
        return $this->hasMany(SchoolMembership::class);
    }

    /**
     * Get the schools this person can work in.
     *
     * @return BelongsToMany<School, $this>
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'school_memberships')
            ->withPivot(['status', 'is_primary', 'joined_at', 'ended_at'])
            ->wherePivot('status', SchoolMembershipStatus::Active->value)
            ->withTimestamps();
    }

    /**
     * Get the school this person opens for organization-level work.
     */
    public function primarySchool(): ?School
    {
        return $this->schoolMemberships()->active()->primary()->first()?->school;
    }

    /**
     * Check if this person can work in the given school.
     */
    public function belongsToSchool(School|int $school): bool
    {
        $schoolId = $school instanceof School ? $school->id : $school;

        return $this->schoolMemberships()->active()->where('school_id', $schoolId)->exists();
    }

    /**
     * Check if this person can work in the school of the current request.
     */
    public function belongsToCurrentSchool(): bool
    {
        $schoolId = current_school_id();

        return $schoolId !== null && $this->belongsToSchool($schoolId);
    }

    /**
     * Check if this person administers the whole platform.
     *
     * Platform access sits outside school roles on purpose. A school role name
     * must never grant access above its own school.
     */
    public function isPlatformAdmin(): bool
    {
        return (bool) $this->is_platform_admin;
    }

    /**
     * Get the studentRecord associated with the User.
     *
     * @return HasOne
     */
    public function studentRecord()
    {
        return $this->hasOne(StudentRecord::class);
    }

    /**
     * Get the studentRecord of graduation associated with the User.
     *
     * @return HasOne
     */
    public function graduatedStudentRecord()
    {
        return $this->hasOne(StudentRecord::class)->withoutGlobalScopes()->where('is_Graduated', true);
    }

    /**
     * Get the studentRecord of graduation associated with the User.
     *
     * @return HasOne
     */
    public function allStudentRecords()
    {
        return $this->hasOne(StudentRecord::class)->withoutGlobalScopes();
    }

    /**
     * The parents that belong to the User.
     *
     * @return BelongsToMany
     */
    public function parents()
    {
        return $this->belongsToMany(ParentRecord::class);
    }

    /**
     * Get the teacherRecord associated with the User.
     *
     * @return HasOne
     */
    public function teacherRecord()
    {
        return $this->hasOne(TeacherRecord::class);
    }

    /**
     * Get the parent records associated with the User.
     *
     * @return HasOne
     */
    public function parentRecord()
    {
        return $this->hasOne(ParentRecord::class);
    }

    /**
     * Get the invitations issued for this account.
     *
     * @return HasMany<AccountInvitation, $this>
     */
    public function accountInvitations(): HasMany
    {
        return $this->hasMany(AccountInvitation::class);
    }

    /**
     * Get the invitation the person can still accept, if one exists.
     */
    public function pendingAccountInvitation(): ?AccountInvitation
    {
        return $this->accountInvitations()->pending()->latest()->first();
    }

    /**
     * Check if the account can sign in and use the dashboard.
     */
    public function hasActiveAccount(): bool
    {
        return $this->account_status->canAccessApplication();
    }

    /**
     * Check if the account is waiting for the person to set a password.
     */
    public function isAwaitingInvitationAcceptance(): bool
    {
        return $this->account_status === AccountStatus::Invited;
    }

    /**
     * Get all of the feeInvoices for the User.
     */
    public function feeInvoices(): HasMany
    {
        return $this->hasMany(FeeInvoice::class);
    }

    // get first name
    public function firstName()
    {
        return explode(' ', $this->name)[0];
    }

    // get first name
    public function getFirstNameAttribute()
    {
        return $this->firstName();
    }

    // get last name
    public function lastName()
    {
        return explode(' ', $this->name)[1];
    }

    // get last name
    public function getLastNameAttribute()
    {
        return $this->lastName();
    }

    // get other names
    public function otherNames()
    {
        $names = array_diff_key(explode(' ', $this->name), array_flip([0, 1]));

        return implode(' ', $names);
    }

    // get other names
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

    // accessor for birthday

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
}
