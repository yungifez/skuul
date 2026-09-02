<?php

namespace App\Models;

use App\Enums\SchoolMembershipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int|null $academic_year_id
 * @property int|null $academic_period_id
 * @property AcademicYear|null $academicYear
 * @property AcademicPeriod|null $academicPeriod
 * @property int|null $calendar_template_id
 * @property CalendarTemplate|null $calendarTemplate
 * @property string $name
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property string|null $postal_code
 * @property Carbon|null $setup_details_completed_at
 */
class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'billing_group_id', 'name', 'address', 'country', 'state', 'city',
        'postal_code', 'code', 'initials', 'phone', 'email', 'logo_path', 'setup_details_completed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'setup_details_completed_at' => 'datetime',
    ];

    /**
     * Get the organization that owns this campus.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the campuses this one keeps a purse with.
     *
     * Null is the normal case: the campus bills on its own.
     *
     * @return BelongsTo<BillingGroup, $this>
     */
    public function billingGroup(): BelongsTo
    {
        return $this->belongsTo(BillingGroup::class);
    }

    /**
     * Check whether a debt should follow a learner to the other campus.
     *
     * Two campuses bill together only when they are named in the same group.
     * Sharing an organization is not enough, and campuses of two organizations
     * never bill together.
     */
    public function billsWith(School $other): bool
    {
        if ($this->id === $other->id) {
            return true;
        }

        return $this->billing_group_id !== null
            && $this->billing_group_id === $other->billing_group_id;
    }

    /**
     * Get the calendar template this campus chose for itself.
     *
     * Null is the normal case: the campus follows its organization. Read
     * `effectiveCalendarTemplate()` to get the template it actually uses.
     *
     * @return BelongsTo<CalendarTemplate, $this>
     */
    public function calendarTemplate(): BelongsTo
    {
        return $this->belongsTo(CalendarTemplate::class);
    }

    /** @return HasOne<SchoolOperatingProfile, $this> */
    public function operatingProfile(): HasOne
    {
        return $this->hasOne(SchoolOperatingProfile::class);
    }

    /**
     * Get the setup phases recorded for this campus.
     *
     * @return HasMany<SchoolSetupPhase, $this>
     */
    public function setupPhases(): HasMany
    {
        return $this->hasMany(SchoolSetupPhase::class);
    }

    /**
     * Get the template this campus generates cycles from.
     *
     * A campus override wins. Otherwise the campus follows its organization's
     * default, which is what keeps reporting across campuses comparable.
     */
    public function effectiveCalendarTemplate(): ?CalendarTemplate
    {
        if ($this->calendar_template_id !== null) {
            return $this->calendarTemplate;
        }

        return CalendarTemplate::where('organization_id', $this->organization_id)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Check if this campus follows a calendar of its own.
     */
    public function overridesOrganizationCalendar(): bool
    {
        return $this->calendar_template_id !== null;
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? Storage::url($this->logo_path) : asset(config('app.logo'));
    }

    /**
     * Get every access record for this school.
     *
     * @return HasMany<SchoolMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(SchoolMembership::class);
    }

    /**
     * Get the staff working-period choices saved for this school.
     *
     * @return HasMany<UserAcademicPeriodPreference, $this>
     */
    public function academicPeriodPreferences(): HasMany
    {
        return $this->hasMany(UserAcademicPeriodPreference::class);
    }

    /**
     * Get the financial periods for this school's books.
     *
     * @return HasMany<FinancialPeriod, $this>
     */
    public function financialPeriods(): HasMany
    {
        return $this->hasMany(FinancialPeriod::class)->orderByDesc('starts_on');
    }

    /**
     * Get the people who can work in this school.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_memberships')
            ->withPivot(['status', 'is_primary', 'joined_at', 'ended_at'])
            ->wherePivot('status', SchoolMembershipStatus::Active->value)
            ->withTimestamps();
    }

    /**
     * Get the AcademicYears for the School.
     *
     * @return HasMany<AcademicYear, $this>
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * Get the reusable levels this campus teaches.
     *
     * @return HasMany<AcademicLevel, $this>
     */
    public function academicLevels(): HasMany
    {
        return $this->hasMany(AcademicLevel::class);
    }

    /**
     * Get every cycle-specific home section for this campus.
     *
     * @return HasMany<AcademicCycleSection, $this>
     */
    public function academicCycleSections(): HasMany
    {
        return $this->hasMany(AcademicCycleSection::class);
    }

    /**
     * Get the academicYear associated with the School.
     *
     * @return HasOne<AcademicYear, $this>
     */
    public function academicYear(): HasOne
    {
        return $this->hasOne(AcademicYear::class, 'id', 'academic_year_id');
    }

    /**
     * Get the academic period associated with the School.
     *
     * @return HasOne<AcademicPeriod, $this>
     */
    public function academicPeriod(): HasOne
    {
        return $this->hasOne(AcademicPeriod::class, 'id', 'academic_period_id');
    }
}
