<?php

namespace App\Enums;

/**
 * The sensitive actions kept in the audit log.
 *
 * Each case is written to `audit_events.action`. Add a case when a new action
 * must be answerable later with "who did this, to what, and when".
 */
enum AuditAction: string
{
    /**
     * A role was given to a user.
     */
    case RoleAttached = 'role.attached';

    /**
     * A role was taken from a user.
     */
    case RoleDetached = 'role.detached';

    /**
     * A permission was given directly to a user or a role.
     */
    case PermissionAttached = 'permission.attached';

    /**
     * A permission was taken from a user or a role.
     */
    case PermissionDetached = 'permission.detached';

    /**
     * An account moved between access states.
     */
    case AccountStatusChanged = 'account.status_changed';

    /**
     * An invitation link was issued and emailed to a person.
     */
    case AccountInvitationSent = 'account_invitation.sent';

    /**
     * Every unused invitation link for an account was stopped.
     */
    case AccountInvitationRevoked = 'account_invitation.revoked';

    /**
     * An enrollment moved between states.
     */
    case EnrollmentStatusChanged = 'enrollment.status_changed';

    /**
     * A student was placed in a class and section.
     */
    case EnrollmentPlaced = 'enrollment.placed';

    /**
     * An enrollment moved to another school.
     */
    case EnrollmentTransferred = 'enrollment.transferred';

    /**
     * A teacher was given a subject to teach.
     */
    case TeachingAssignmentCreated = 'teaching_assignment.created';

    /**
     * A teaching assignment was ended.
     */
    case TeachingAssignmentEnded = 'teaching_assignment.ended';

    /**
     * A subject offering was configured for one academic period.
     */
    case CourseOfferingCreated = 'course_offering.created';

    /**
     * A subject offering moved between draft, active, and archived states.
     */
    case CourseOfferingStatusChanged = 'course_offering.status_changed';

    /**
     * A timetable revision was published.
     */
    case TimetablePublished = 'timetable.published';

    /**
     * A timetable revision was archived.
     */
    case TimetableArchived = 'timetable.archived';

    /**
     * A new timetable revision was started from a published one.
     */
    case TimetableRevised = 'timetable.revised';

    /**
     * A balanced entry was written into the books.
     */
    case LedgerTransactionPosted = 'ledger.posted';

    /**
     * A notice was put on the board.
     */
    case NoticePublished = 'notice.published';

    /**
     * A notice was held for a later day.
     */
    case NoticeScheduled = 'notice.scheduled';

    /**
     * A notice passed its last day.
     */
    case NoticeExpired = 'notice.expired';

    /**
     * Somebody asked for a report.
     */
    case ReportRequested = 'report.requested';

    /**
     * Somebody downloaded a report file.
     */
    case ReportDownloaded = 'report.downloaded';

    /**
     * A feature was turned on for a school.
     */
    case FeatureEnabled = 'feature.enabled';

    /**
     * A feature was turned off for a school.
     */
    case FeatureDisabled = 'feature.disabled';

    /**
     * A behaviour or safeguarding case was recorded.
     */
    case IncidentReported = 'incident.reported';

    /**
     * A case moved between states.
     */
    case IncidentStatusChanged = 'incident.status_changed';

    /**
     * An academic year or academic period was opened, closed, or reopened.
     */
    case AcademicPeriodStatusChanged = 'academic_period.status_changed';

    /**
     * A cycle was generated from a calendar template.
     */
    case AcademicCycleGenerated = 'academic_cycle.generated';

    /**
     * The dates of a period that was already in use were changed.
     */
    case AcademicPeriodDatesChanged = 'academic_period.dates_changed';

    /**
     * A campus stopped following its organization's calendar, or resumed it.
     */
    case CampusCalendarOverridden = 'campus_calendar.overridden';

    /**
     * A calendar template was created or changed.
     */
    case CalendarTemplateSaved = 'calendar_template.saved';

    /**
     * A campus chose the way it teaches an academic cycle.
     */
    case InstructionalModelChanged = 'instructional_model.changed';

    /**
     * A reusable academic level was added for a campus.
     */
    case AcademicLevelCreated = 'academic_level.created';

    /**
     * A reusable academic level was renamed or re-linked.
     */
    case AcademicLevelUpdated = 'academic_level.updated';

    /**
     * A reusable academic level moved between lifecycle states.
     */
    case AcademicLevelStatusChanged = 'academic_level.status_changed';

    /**
     * A cycle-specific home section was configured.
     */
    case AcademicCycleSectionCreated = 'academic_cycle_section.created';

    /**
     * A cycle-specific home section had its setup changed.
     */
    case AcademicCycleSectionUpdated = 'academic_cycle_section.updated';

    /**
     * A cycle-specific home section moved between lifecycle states.
     */
    case AcademicCycleSectionStatusChanged = 'academic_cycle_section.status_changed';

    /**
     * Section structure was copied into another academic cycle.
     */
    case AcademicCycleSectionsRolledForward = 'academic_cycle_section.rolled_forward';

    /**
     * A result was published from the gradebook.
     */
    case ResultPublished = 'result.published';

    /**
     * A published result was corrected with a new revision.
     */
    case ResultRevised = 'result.revised';

    /**
     * A support plan was written for a child.
     */
    case SupportPlanOpened = 'support_plan.opened';

    /**
     * A support plan moved between states.
     */
    case SupportPlanStatusChanged = 'support_plan.status_changed';

    /**
     * A child's health record was written or changed.
     */
    case HealthRecordUpdated = 'health_record.updated';

    /**
     * A member of staff asked for days away.
     */
    case StaffLeaveRequested = 'staff_leave.requested';

    /**
     * A leave request was answered or recorded as taken.
     */
    case StaffLeaveStatusChanged = 'staff_leave.status_changed';

    /**
     * One school asked another for a student's records.
     */
    case DataSharingRequested = 'data_sharing.requested';

    /**
     * A request to share records was answered or taken back.
     */
    case DataSharingStatusChanged = 'data_sharing.status_changed';

    /**
     * Records were handed over in a transfer package.
     */
    case TransferPackageBuilt = 'transfer_package.built';

    /**
     * A transfer package was taken in by the school that asked for it.
     */
    case TransferPackageReceived = 'transfer_package.received';

    /**
     * An organization was created or updated.
     */
    case OrganizationCreated = 'organization.created';

    case OrganizationUpdated = 'organization.updated';

    /**
     * A campus was assigned to an organization.
     */
    case SchoolOrganizationAssigned = 'school.organization_assigned';

    /**
     * A person was granted organization administration.
     */
    case OrganizationMembershipGranted = 'organization_membership.granted';

    /**
     * A person's organization scope was taken away. School access is kept.
     */
    case OrganizationMembershipRevoked = 'organization_membership.revoked';

    /**
     * The permissions delegated to an organization member were changed.
     */
    case OrganizationMembershipPermissionsChanged = 'organization_membership.permissions_changed';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::RoleAttached => 'Role given',
            self::RoleDetached => 'Role removed',
            self::PermissionAttached => 'Permission given',
            self::PermissionDetached => 'Permission removed',
            self::AccountStatusChanged => 'Account status changed',
            self::AccountInvitationSent => 'Invitation sent',
            self::AccountInvitationRevoked => 'Invitation revoked',
            self::EnrollmentStatusChanged => 'Enrollment status changed',
            self::EnrollmentPlaced => 'Student placed in a class',
            self::EnrollmentTransferred => 'Enrollment transferred',
            self::TeachingAssignmentCreated => 'Teacher assigned to a subject',
            self::TeachingAssignmentEnded => 'Teaching assignment ended',
            self::CourseOfferingCreated => 'Course offering created',
            self::CourseOfferingStatusChanged => 'Course offering status changed',
            self::TimetablePublished => 'Timetable published',
            self::TimetableArchived => 'Timetable archived',
            self::TimetableRevised => 'Timetable revision started',
            self::LedgerTransactionPosted => 'Ledger entry posted',
            self::NoticePublished => 'Notice published',
            self::NoticeScheduled => 'Notice scheduled',
            self::NoticeExpired => 'Notice expired',
            self::ReportRequested => 'Report requested',
            self::ReportDownloaded => 'Report downloaded',
            self::FeatureEnabled => 'Feature turned on',
            self::FeatureDisabled => 'Feature turned off',
            self::IncidentReported => 'Case recorded',
            self::IncidentStatusChanged => 'Case status changed',
            self::AcademicPeriodStatusChanged => 'Academic period status changed',
            self::AcademicCycleGenerated => 'Academic cycle generated',
            self::AcademicPeriodDatesChanged => 'Academic period dates changed',
            self::CampusCalendarOverridden => 'Campus calendar changed',
            self::CalendarTemplateSaved => 'Calendar template saved',
            self::InstructionalModelChanged => 'Instructional model changed',
            self::AcademicLevelCreated => 'Academic level created',
            self::AcademicLevelUpdated => 'Academic level updated',
            self::AcademicLevelStatusChanged => 'Academic level status changed',
            self::AcademicCycleSectionCreated => 'Academic cycle section created',
            self::AcademicCycleSectionUpdated => 'Academic cycle section updated',
            self::AcademicCycleSectionStatusChanged => 'Academic cycle section status changed',
            self::AcademicCycleSectionsRolledForward => 'Academic cycle sections rolled forward',
            self::ResultPublished => 'Result published',
            self::ResultRevised => 'Result corrected',
            self::SupportPlanOpened => 'Support plan opened',
            self::SupportPlanStatusChanged => 'Support plan status changed',
            self::HealthRecordUpdated => 'Health record updated',
            self::StaffLeaveRequested => 'Leave requested',
            self::StaffLeaveStatusChanged => 'Leave status changed',
            self::DataSharingRequested => 'Records requested',
            self::DataSharingStatusChanged => 'Records request answered',
            self::TransferPackageBuilt => 'Records handed over',
            self::TransferPackageReceived => 'Records taken in',
            self::OrganizationCreated => 'Organization created',
            self::OrganizationUpdated => 'Organization updated',
            self::SchoolOrganizationAssigned => 'School assigned to organization',
            self::OrganizationMembershipGranted => 'Organization administrator granted',
            self::OrganizationMembershipRevoked => 'Organization administrator revoked',
            self::OrganizationMembershipPermissionsChanged => 'Organization permissions changed',
        };
    }
}
