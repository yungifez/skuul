<?php

use App\Http\Controllers\AcademicCycleController;
use App\Http\Controllers\AcademicCycleSectionController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmissionWaitlistController;
use App\Http\Controllers\BoardingPlaceController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CalendarTemplateController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\CustomTimetableItemController;
use App\Http\Controllers\DormitoryController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamSlotController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FeeInvoiceController;
use App\Http\Controllers\FeeInvoiceRecordController;
use App\Http\Controllers\GradebookController;
use App\Http\Controllers\GradingScaleController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\LibraryCopyController;
use App\Http\Controllers\LibraryLendingRulesController;
use App\Http\Controllers\LibraryLoanController;
use App\Http\Controllers\LibraryReservationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NoticeAttachmentController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NoticeNotificationPreferenceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationDashboardController;
use App\Http\Controllers\OvernightLeaveController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentAccountController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableTimeSlotController;
use App\Http\Middleware\ResolveDomainContext;
use App\Http\Middleware\SetActiveAcademicPeriod;
use App\Http\Middleware\SetActiveSchool;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the application bootstrap within a group which
| contains the "web" middleware group. Now create something great!
|
| Edit: haha we built something great
| Should I add easter eggs?
|
*/

// The load balancer and the operations dashboard read this.
Route::get('/health', HealthController::class)->name('health');

Route::get('/install', [InstallationController::class, 'index'])->name('install.index');
Route::post('/install/key', [InstallationController::class, 'generateKey'])
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
        ResolveDomainContext::class,
        SetActiveSchool::class,
        SetActiveAcademicPeriod::class,
    ])
    ->name('install.key');
Route::post('/install/database/test', [InstallationController::class, 'testDatabase'])->name('install.database.test');
Route::post('/install/database/setup', [InstallationController::class, 'setupDatabase'])->name('install.database.setup');
Route::post('/install/world/setup', [InstallationController::class, 'setupWorldData'])->name('install.world.setup');
Route::get('/locations/states', [LocationController::class, 'states'])->name('locations.states');
Route::get('/locations/cities', [LocationController::class, 'cities'])->name('locations.cities');
Route::post('/install', [InstallationController::class, 'store'])->name('install.store');

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/home', function () {
    return redirect()->route('dashboard');
});

// Accounts are provisioned by an administrator. There is no public registration.
Route::middleware(['guest'])->group(function () {
    Route::get('/invitations/{token}', ['App\Http\Controllers\AccountInvitationController', 'show'])->name('invitations.show');
    Route::post('/invitations/{token}', ['App\Http\Controllers\AccountInvitationController', 'accept'])->name('invitations.accept');
});

// user must be authenticated
Route::middleware('auth', 'verified', 'App\Http\Middleware\EnsureAccountIsActive', 'App\Http\Middleware\PreventGraduatedStudent')->prefix('dashboard')->namespace('App\Http\Controllers')->group(function () {
    // Families use portal authorization, not a staff working-school membership.
    Route::get('portal/overview', ['App\Http\Controllers\PortalOverviewController', 'index'])->name('portal.overview');
    Route::get('portal/notification-preferences', [NoticeNotificationPreferenceController::class, 'portalEdit'])->name('portal.notification-preferences.edit');
    Route::put('portal/notification-preferences', [NoticeNotificationPreferenceController::class, 'portalUpdate'])->name('portal.notification-preferences.update');
    Route::get('portal/enrollments/{studentRecord}/attendance', ['App\Http\Controllers\PortalAttendanceController', 'show'])->name('portal.attendance.show');
    Route::get('portal/enrollments/{studentRecord}/calendar', ['App\Http\Controllers\PortalCalendarController', 'index'])->name('portal.calendar.index');
    Route::get('portal/enrollments/{studentRecord}/documents', ['App\Http\Controllers\PortalDocumentsController', 'index'])->name('portal.documents.index');
    Route::get('portal/enrollments/{studentRecord}/documents/report-cards/{reportCardSnapshot}', ['App\Http\Controllers\PortalDocumentsController', 'downloadReportCard'])->name('portal.documents.report-cards.download');
    Route::get('portal/enrollments/{studentRecord}/documents/transcripts/{transcriptSnapshot}', ['App\Http\Controllers\PortalDocumentsController', 'downloadTranscript'])->name('portal.documents.transcripts.download');
    Route::get('portal/enrollments/{studentRecord}/boarding', ['App\Http\Controllers\PortalBoardingController', 'index'])->name('portal.boarding.index');
    Route::get('portal/enrollments/{studentRecord}/notices', ['App\Http\Controllers\PortalNoticeController', 'index'])->name('portal.notices.index');
    Route::get('portal/enrollments/{studentRecord}/library', ['App\Http\Controllers\PortalLibraryController', 'index'])->name('portal.library.index');
    Route::get('portal/enrollments/{studentRecord}/requests', ['App\Http\Controllers\PortalRequestController', 'index'])->name('portal.requests.index');
    Route::post('portal/enrollments/{studentRecord}/requests', ['App\Http\Controllers\PortalRequestController', 'store'])->name('portal.requests.store');
    Route::get('notices/{notice}/attachment', NoticeAttachmentController::class)->name('notices.attachments.download');
    Route::get('notice-preferences', [NoticeNotificationPreferenceController::class, 'edit'])->middleware('App\Http\Middleware\RequireActiveSchool')->name('notice-preferences.edit');
    Route::put('notice-preferences', [NoticeNotificationPreferenceController::class, 'update'])->middleware('App\Http\Middleware\RequireActiveSchool')->name('notice-preferences.update');
    // Organization administration is separate from working-school access.
    Route::get('organizations/{organization}/members', ['App\Http\Controllers\OrganizationMemberController', 'index'])->name('organizations.members.index');
    Route::get('organizations/{organization}/domains', ['App\Http\Controllers\OrganizationDomainController', 'index'])->name('organizations.domains.index');
    Route::post('organizations/{organization}/domains', ['App\Http\Controllers\OrganizationDomainController', 'store'])->name('organizations.domains.store');
    Route::post('organizations/{organization}/domains/{domain}/verify', ['App\Http\Controllers\OrganizationDomainController', 'verify'])->name('organizations.domains.verify');
    Route::delete('organizations/{organization}/domains/{domain}', ['App\Http\Controllers\OrganizationDomainController', 'destroy'])->name('organizations.domains.destroy');
    Route::get('organizations/{organization}/billing-groups', ['App\Http\Controllers\OrganizationBillingGroupController', 'index'])->name('organizations.billing-groups.index');
    Route::post('organizations/{organization}/billing-groups', ['App\Http\Controllers\OrganizationBillingGroupController', 'store'])->name('organizations.billing-groups.store');
    Route::put('organizations/{organization}/campuses/{school}/billing', ['App\Http\Controllers\OrganizationBillingGroupController', 'update'])->name('organizations.billing-groups.update');
    Route::resource('organizations', OrganizationController::class)->except('destroy');
    Route::get('organizations/{organization}/dashboard', OrganizationDashboardController::class)->name('organizations.dashboard');
    Route::resource('organizations.calendar-templates', CalendarTemplateController::class)
        ->parameters(['calendar-templates' => 'calendarTemplate'])
        ->except(['show', 'destroy']);
    Route::post('organizations/{organization}/calendar-templates/{calendarTemplate}/cycles', [AcademicCycleController::class, 'store'])->name('organizations.calendar-templates.cycles.store');
    Route::post('organizations/{organization}/calendar-templates/{calendarTemplate}/campuses/{school}', [CalendarTemplateController::class, 'overrideCampus'])->name('organizations.calendar-templates.campuses.override');
    Route::delete('organizations/{organization}/calendar-templates/{calendarTemplate}/campuses/{school}', [CalendarTemplateController::class, 'inheritCampus'])->name('organizations.calendar-templates.campuses.inherit');

    // roles a campus writes for itself. Role work is campus work, so it needs
    // a working school like any other campus screen.
    Route::middleware('App\Http\Middleware\RequireActiveSchool')->group(function () {
        Route::get('roles', ['App\Http\Controllers\CampusRoleController', 'index'])->name('roles.index');
        Route::get('roles/create', ['App\Http\Controllers\CampusRoleController', 'create'])->name('roles.create');
        Route::post('roles', ['App\Http\Controllers\CampusRoleController', 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', ['App\Http\Controllers\CampusRoleController', 'edit'])->name('roles.edit');
        Route::put('roles/{role}', ['App\Http\Controllers\CampusRoleController', 'update'])->name('roles.update');
        Route::post('roles/{role}/copy', ['App\Http\Controllers\CampusRoleController', 'duplicate'])->name('roles.duplicate');
        Route::post('roles/{role}/archive', ['App\Http\Controllers\CampusRoleController', 'archive'])->name('roles.archive');
        Route::post('roles/{role}/restore', ['App\Http\Controllers\CampusRoleController', 'restore'])->name('roles.restore');
        Route::post('roles/{role}/members', ['App\Http\Controllers\CampusRoleController', 'give'])->name('roles.members.store');
        Route::delete('roles/{role}/members', ['App\Http\Controllers\CampusRoleController', 'take'])->name('roles.members.destroy');
    });

    // manage school settings
    Route::get('schools/settings', ['App\Http\Controllers\SchoolController', 'settings'])->name('schools.settings')->middleware('App\Http\Middleware\RequireActiveSchool');
    Route::get('schools/operating-profile', ['App\Http\Controllers\SchoolOperatingProfileController', 'edit'])->name('schools.operating-profile.edit')->middleware('App\Http\Middleware\RequireActiveSchool');
    Route::put('schools/operating-profile', ['App\Http\Controllers\SchoolOperatingProfileController', 'update'])->name('schools.operating-profile.update')->middleware('App\Http\Middleware\RequireActiveSchool');
    Route::get('schools/features', ['App\Http\Controllers\FeatureSettingsController', 'edit'])->name('schools.features.edit')->middleware('App\Http\Middleware\RequireActiveSchool');
    Route::put('schools/features', ['App\Http\Controllers\FeatureSettingsController', 'update'])->name('schools.features.update')->middleware('App\Http\Middleware\RequireActiveSchool');

    // School routes
    Route::resource('schools', SchoolController::class);
    Route::post('schools/set-school', ['App\Http\Controllers\SchoolController', 'setSchool'])->name('schools.setSchool');

    // super admin must have school id set
    Route::middleware(['App\Http\Middleware\RequireActiveSchool'])->group(function () {
        Route::resource('grading-scales', GradingScaleController::class)
            ->parameters(['grading-scales' => 'gradingScale'])
            ->only(['index', 'store', 'update', 'destroy']);

        // dashboard route
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard')->withoutMiddleware(['App\Http\Middleware\PreventGraduatedStudent']);

        // Academic structure. Levels are reusable; cycle sections belong to
        // one academic cycle and one level.
        Route::resource('academic-levels', AcademicLevelController::class)
            ->parameters(['academic-levels' => 'academicLevel'])
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::put('academic-levels/{academicLevel}/status', [AcademicLevelController::class, 'changeStatus'])
            ->name('academic-levels.status.update');

        // The roll-forward review page is registered before the resource so
        // "roll-forward" is not read as a cycle section key.
        Route::get('academic-cycle-sections/roll-forward', [AcademicCycleSectionController::class, 'rollForwardForm'])
            ->name('academic-cycle-sections.roll-forward.show');
        Route::post('academic-cycle-sections/roll-forward', [AcademicCycleSectionController::class, 'rollForward'])
            ->name('academic-cycle-sections.roll-forward');
        Route::resource('academic-cycle-sections', AcademicCycleSectionController::class)
            ->parameters(['academic-cycle-sections' => 'academicCycleSection'])
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::put('academic-cycle-sections/{academicCycleSection}/status', [AcademicCycleSectionController::class, 'changeStatus'])
            ->name('academic-cycle-sections.status.update');

        Route::get('admissions/waitlist', [AdmissionWaitlistController::class, 'index'])->name('admissions.waitlist.index');
        Route::post('admissions/waitlist', [AdmissionWaitlistController::class, 'store'])->name('admissions.waitlist.store');
        Route::post('admissions/waitlist/{admissionWaitlistEntry}/offer', [AdmissionWaitlistController::class, 'offer'])->name('admissions.waitlist.offer');
        Route::post('admissions/waitlist/{admissionWaitlistEntry}/accept', [AdmissionWaitlistController::class, 'accept'])->name('admissions.waitlist.accept');
        Route::post('admissions/waitlist/{admissionWaitlistEntry}/decline', [AdmissionWaitlistController::class, 'decline'])->name('admissions.waitlist.decline');

        // report routes. A report reads whatever period it is given, so it
        // does not need one to be set first.
        Route::get('reports', ['App\Http\Controllers\ReportController', 'index'])->name('reports.index');
        Route::post('reports', ['App\Http\Controllers\ReportController', 'store'])->name('reports.store');
        Route::get('report-cards', ['App\Http\Controllers\ReportCardController', 'index'])->name('report-cards.index');
        Route::post('report-cards', ['App\Http\Controllers\ReportCardController', 'store'])->name('report-cards.store');
        Route::get('report-cards/{reportCardSnapshot}', ['App\Http\Controllers\ReportCardController', 'show'])->name('report-cards.show');
        Route::get('transcripts', ['App\Http\Controllers\TranscriptController', 'index'])->name('transcripts.index');
        Route::post('transcripts', ['App\Http\Controllers\TranscriptController', 'store'])->name('transcripts.store');
        Route::get('attendance/register', ['App\Http\Controllers\AttendanceRegisterController', 'index'])->name('attendance.register');
        Route::post('attendance/register', ['App\Http\Controllers\AttendanceRegisterController', 'store'])->name('attendance.register.store');
        Route::get('reports/{reportRun}/download', ['App\Http\Controllers\ReportController', 'download'])->name('reports.download');

        // discipline routes. A case is written against the day it happened, so
        // it does not need a period to be set first. A school that turned
        // discipline off closes the way in without losing the cases it holds.
        Route::middleware(['feature:discipline'])->group(function () {
            Route::get('incidents', ['App\Http\Controllers\IncidentController', 'index'])->name('incidents.index');
            Route::get('incidents/create', ['App\Http\Controllers\IncidentController', 'create'])->name('incidents.create');
            Route::post('incidents', ['App\Http\Controllers\IncidentController', 'store'])->name('incidents.store');
            Route::get('incidents/{incident}', ['App\Http\Controllers\IncidentController', 'show'])->name('incidents.show');
            Route::post('incidents/{incident}/notes', ['App\Http\Controllers\IncidentController', 'storeNote'])->name('incidents.notes.store');
            Route::put('incidents/{incident}/status', ['App\Http\Controllers\IncidentController', 'changeStatus'])->name('incidents.status.update');
            Route::post('incidents/{incident}/actions', ['App\Http\Controllers\IncidentController', 'storeAction'])->name('incidents.actions.store');
            Route::post('incidents/{incident}/actions/{incidentAction}/complete', ['App\Http\Controllers\IncidentController', 'completeAction'])->name('incidents.actions.complete');
        });

        // wellbeing routes. A plan of help runs across periods, so it does not
        // need a period to be set first. Health facts are kept apart from the
        // student profile, because reading a profile must not open them.
        Route::middleware(['feature:wellbeing'])->group(function () {
            Route::get('support-plans', ['App\Http\Controllers\SupportPlanController', 'index'])->name('support-plans.index');
            Route::get('support-plans/create', ['App\Http\Controllers\SupportPlanController', 'create'])->name('support-plans.create');
            Route::post('support-plans', ['App\Http\Controllers\SupportPlanController', 'store'])->name('support-plans.store');
            Route::get('support-plans/{supportPlan}', ['App\Http\Controllers\SupportPlanController', 'show'])->name('support-plans.show');
            Route::put('support-plans/{supportPlan}/status', ['App\Http\Controllers\SupportPlanController', 'changeStatus'])->name('support-plans.status.update');
            Route::post('support-plans/{supportPlan}/actions', ['App\Http\Controllers\SupportPlanController', 'storeAction'])->name('support-plans.actions.store');
            Route::post('support-plans/{supportPlan}/actions/{supportPlanAction}/complete', ['App\Http\Controllers\SupportPlanController', 'completeAction'])->name('support-plans.actions.complete');
            Route::post('support-plans/{supportPlan}/notes', ['App\Http\Controllers\SupportPlanController', 'storeNote'])->name('support-plans.notes.store');

            Route::get('health-records', ['App\Http\Controllers\StudentHealthRecordController', 'index'])->name('health-records.index');
            Route::get('health-records/{studentRecord}', ['App\Http\Controllers\StudentHealthRecordController', 'edit'])->name('health-records.edit');
            Route::put('health-records/{studentRecord}', ['App\Http\Controllers\StudentHealthRecordController', 'update'])->name('health-records.update');
        });

        // staff routes. Employment is not teaching, so these routes do not need
        // an academic period. A school that turned staff operations off closes
        // the way in without losing the records it holds.
        Route::middleware(['feature:staff_operations'])->group(function () {
            Route::get('staff-profiles', ['App\Http\Controllers\StaffProfileController', 'index'])->name('staff-profiles.index');
            Route::get('staff-profiles/create', ['App\Http\Controllers\StaffProfileController', 'create'])->name('staff-profiles.create');
            Route::post('staff-profiles', ['App\Http\Controllers\StaffProfileController', 'store'])->name('staff-profiles.store');
            Route::get('staff-profiles/{staffProfile}', ['App\Http\Controllers\StaffProfileController', 'show'])->name('staff-profiles.show');
            Route::put('staff-profiles/{staffProfile}', ['App\Http\Controllers\StaffProfileController', 'update'])->name('staff-profiles.update');
            Route::post('staff-profiles/{staffProfile}/credentials', ['App\Http\Controllers\StaffProfileController', 'storeCredential'])->name('staff-profiles.credentials.store');
            Route::post('staff-profiles/{staffProfile}/availabilities', ['App\Http\Controllers\StaffProfileController', 'storeAvailability'])->name('staff-profiles.availabilities.store');

            Route::get('staff-leave', ['App\Http\Controllers\StaffLeaveRequestController', 'index'])->name('staff-leave.index');
            Route::post('staff-leave', ['App\Http\Controllers\StaffLeaveRequestController', 'store'])->name('staff-leave.store');
            Route::put('staff-leave/{staffLeaveRequest}/status', ['App\Http\Controllers\StaffLeaveRequestController', 'changeStatus'])->name('staff-leave.status.update');
        });

        // data sharing routes. Asking, approving, and handing over are three
        // decisions, and the receiving school still has to take the package in.
        // Both schools read the request, so these routes are not scoped to one.
        Route::get('data-sharing-requests', ['App\Http\Controllers\DataSharingRequestController', 'index'])->name('data-sharing-requests.index');
        Route::get('data-sharing-requests/create', ['App\Http\Controllers\DataSharingRequestController', 'create'])->name('data-sharing-requests.create');
        Route::post('data-sharing-requests', ['App\Http\Controllers\DataSharingRequestController', 'store'])->name('data-sharing-requests.store');
        Route::get('data-sharing-requests/{dataSharingRequest}', ['App\Http\Controllers\DataSharingRequestController', 'show'])->name('data-sharing-requests.show');
        Route::put('data-sharing-requests/{dataSharingRequest}/status', ['App\Http\Controllers\DataSharingRequestController', 'changeStatus'])->name('data-sharing-requests.status.update');
        Route::post('data-sharing-requests/{dataSharingRequest}/fulfil', ['App\Http\Controllers\DataSharingRequestController', 'fulfil'])->name('data-sharing-requests.fulfil');
        Route::post('data-sharing-requests/{dataSharingRequest}/packages/{transferPackage}/receive', ['App\Http\Controllers\DataSharingRequestController', 'receive'])->name('data-sharing-requests.packages.receive');

        // portal request routes. A family asks through the portal; the school
        // reads and answers here. The portal feature gates the family side of
        // this, not the school's inbox, so a school that closes the portal can
        // still finish what it was already asked.
        Route::get('portal-requests', ['App\Http\Controllers\PortalRequestController', 'inbox'])->name('portal-requests.index');
        Route::put('portal-requests/{portalRequest}/status', ['App\Http\Controllers\PortalRequestController', 'changeStatus'])->name('portal-requests.status.update');

        // graduation plan routes. A plan says what a learner must finish, and
        // only a published result counts towards it. Graduation is not one of
        // the features a school can turn off; the permissions decide who keeps
        // the plans.
        Route::get('graduation-plans', ['App\Http\Controllers\GraduationPlanController', 'index'])->name('graduation-plans.index');
        Route::get('graduation-plans/create', ['App\Http\Controllers\GraduationPlanController', 'create'])->name('graduation-plans.create');
        Route::post('graduation-plans', ['App\Http\Controllers\GraduationPlanController', 'store'])->name('graduation-plans.store');
        Route::get('graduation-plans/{graduationPlan}', ['App\Http\Controllers\GraduationPlanController', 'show'])->name('graduation-plans.show');
        Route::put('graduation-plans/{graduationPlan}', ['App\Http\Controllers\GraduationPlanController', 'update'])->name('graduation-plans.update');
        Route::post('graduation-plans/{graduationPlan}/requirements', ['App\Http\Controllers\GraduationPlanController', 'storeRequirement'])->name('graduation-plans.requirements.store');
        Route::delete('graduation-plans/{graduationPlan}/requirements/{graduationRequirement}', ['App\Http\Controllers\GraduationPlanController', 'destroyRequirement'])->name('graduation-plans.requirements.destroy');
        Route::post('graduation-plans/{graduationPlan}/exemptions', ['App\Http\Controllers\GraduationPlanController', 'storeExemption'])->name('graduation-plans.exemptions.store');
        Route::delete('graduation-plans/{graduationPlan}/exemptions/{graduationExemption}', ['App\Http\Controllers\GraduationPlanController', 'destroyExemption'])->name('graduation-plans.exemptions.destroy');

        // ranking routes. A position is worked out when it is asked for, so
        // there is nothing to write and nothing to store. A school that does
        // not rank children closes the screen entirely.
        Route::middleware(['feature:ranking'])->group(function () {
            Route::get('rankings', ['App\Http\Controllers\RankingController', 'index'])->name('rankings.index');
        });

        // calendar routes. An event names its own days, so it does not need a
        // period to be set first. A school that turned events off closes the
        // way in without losing the calendar it holds.
        Route::middleware(['feature:events'])->group(function () {
            Route::get('calendar-events', ['App\Http\Controllers\CalendarEventController', 'index'])->name('calendar-events.index');
            Route::get('calendar-events/create', ['App\Http\Controllers\CalendarEventController', 'create'])->name('calendar-events.create');
            Route::post('calendar-events', ['App\Http\Controllers\CalendarEventController', 'store'])->name('calendar-events.store');
            Route::get('calendar-events/{calendarEvent}', ['App\Http\Controllers\CalendarEventController', 'edit'])->name('calendar-events.edit');
            Route::put('calendar-events/{calendarEvent}', ['App\Http\Controllers\CalendarEventController', 'update'])->name('calendar-events.update');
            Route::put('calendar-events/{calendarEvent}/publication', ['App\Http\Controllers\CalendarEventController', 'changePublication'])->name('calendar-events.publication.update');
            Route::delete('calendar-events/{calendarEvent}', ['App\Http\Controllers\CalendarEventController', 'destroy'])->name('calendar-events.destroy');
        });

        // cohort and programme routes. A group of people is not a class, so
        // these routes do not need an academic period. Cohorts are not one of
        // the features a school can turn off; the permissions decide who sees
        // them.
        Route::get('cohorts', ['App\Http\Controllers\CohortController', 'index'])->name('cohorts.index');
        Route::get('cohorts/create', ['App\Http\Controllers\CohortController', 'create'])->name('cohorts.create');
        Route::post('cohorts', ['App\Http\Controllers\CohortController', 'store'])->name('cohorts.store');
        Route::get('cohorts/{cohort}', ['App\Http\Controllers\CohortController', 'show'])->name('cohorts.show');
        Route::put('cohorts/{cohort}', ['App\Http\Controllers\CohortController', 'update'])->name('cohorts.update');
        Route::post('cohorts/{cohort}/members', ['App\Http\Controllers\CohortController', 'storeMember'])->name('cohorts.members.store');
        Route::delete('cohorts/{cohort}/members/{cohortMember}', ['App\Http\Controllers\CohortController', 'removeMember'])->name('cohorts.members.destroy');

        Route::get('programs', ['App\Http\Controllers\ProgramController', 'index'])->name('programs.index');
        Route::get('programs/create', ['App\Http\Controllers\ProgramController', 'create'])->name('programs.create');
        Route::post('programs', ['App\Http\Controllers\ProgramController', 'store'])->name('programs.store');
        Route::get('programs/{program}', ['App\Http\Controllers\ProgramController', 'show'])->name('programs.show');
        Route::post('programs/{program}/participations', ['App\Http\Controllers\ProgramController', 'storeParticipation'])->name('programs.participations.store');
        Route::put('programs/{program}/participations/{programParticipation}', ['App\Http\Controllers\ProgramController', 'updateParticipation'])->name('programs.participations.update');

        // import routes. An import reads the school, not the period, so it
        // does not need a period to be set first. A school that turned imports
        // off closes the way in without losing what it already imported.
        Route::middleware(['feature:imports'])->group(function () {
            Route::get('imports', ['App\Http\Controllers\ImportController', 'index'])->name('imports.index');
            Route::post('imports', ['App\Http\Controllers\ImportController', 'store'])->name('imports.store');
            Route::get('imports/{importBatch}', ['App\Http\Controllers\ImportController', 'show'])->name('imports.show');
            Route::post('imports/{importBatch}/apply', ['App\Http\Controllers\ImportController', 'apply'])->name('imports.apply');
            Route::post('imports/{importBatch}/cancel', ['App\Http\Controllers\ImportController', 'cancel'])->name('imports.cancel');
        });

        Route::middleware(['App\Http\Middleware\EnsureAcademicYearIsSet', 'App\Http\Middleware\CreateCurrentAcademicYearRecord'])->group(function () {
            Route::resource('course-offerings', CourseOfferingController::class)->only(['index', 'create', 'store']);
            Route::post('course-offerings/{courseOffering}/activate', ['App\Http\Controllers\CourseOfferingController', 'activate'])->name('course-offerings.activate');
            Route::post('course-offerings/{courseOffering}/teachers', ['App\Http\Controllers\CourseOfferingController', 'assignTeacher'])->name('course-offerings.teachers.store');
            Route::get('course-offerings/{courseOffering}/gradebook', [GradebookController::class, 'show'])->name('course-offerings.gradebook.show');
            Route::post('course-offerings/{courseOffering}/gradebook/templates', [GradebookController::class, 'storeAssessmentTemplate'])->name('course-offerings.gradebook.templates.store');
            Route::post('course-offerings/{courseOffering}/gradebook/templates/apply', [GradebookController::class, 'applyAssessmentTemplate'])->name('course-offerings.gradebook.templates.apply');
            Route::post('course-offerings/{courseOffering}/gradebook/items', [GradebookController::class, 'storeItem'])->name('course-offerings.gradebook.items.store');
            Route::post('course-offerings/{courseOffering}/gradebook/entries', [GradebookController::class, 'storeEntry'])->name('course-offerings.gradebook.entries.store');
            Route::post('course-offerings/{courseOffering}/gradebook/results', [GradebookController::class, 'publish'])->name('course-offerings.gradebook.results.publish');
            Route::post('course-offerings/{courseOffering}/gradebook/results/approve', [GradebookController::class, 'approve'])->name('course-offerings.gradebook.results.approve');
            Route::post('course-offerings/{courseOffering}/gradebook/results/reject', [GradebookController::class, 'reject'])->name('course-offerings.gradebook.results.reject');

            // promotion routes
            Route::get('students/promotions', ['App\Http\Controllers\PromotionController', 'index'])->name('students.promotions');
            Route::get('students/promote', ['App\Http\Controllers\PromotionController', 'promoteView'])->name('students.promote');
            Route::post('students/promote', ['App\Http\Controllers\PromotionController', 'promote']);
            Route::get('students/promotions/{promotion}', ['App\Http\Controllers\PromotionController', 'show'])->name('students.promotions.show');
            Route::delete('students/promotions/{promotion}/reset', ['App\Http\Controllers\PromotionController', 'resetPromotion'])->name('students.promotions.reset');

            // campus move routes. A campus decides the moves arriving at it.
            Route::get('students/campus-moves', ['App\Http\Controllers\CampusMoveRequestController', 'index'])->name('campus-moves.index');

            // graduation routes
            Route::get('students/graduations', ['App\Http\Controllers\GraduationController', 'index'])->name('students.graduations');
            Route::get('students/graduate', ['App\Http\Controllers\GraduationController', 'graduateView'])->name('students.graduate');
            Route::post('students/graduate', ['App\Http\Controllers\GraduationController', 'graduate']);
            Route::delete('students/graduations/{student}/reset', ['App\Http\Controllers\GraduationController', 'resetGraduation'])->name('students.graduations.reset');

            // academic period routes
            Route::get('academic-periods', [AcademicPeriodController::class, 'index'])->name('academic-periods.index');
            Route::post('academic-periods/set', ['App\Http\Controllers\AcademicPeriodController', 'setAcademicPeriod'])->name('academic-periods.set-academic-period');
            Route::post('academic-periods/{academicPeriod}/close', ['App\Http\Controllers\AcademicPeriodController', 'close'])->name('academic-periods.close');
            Route::post('academic-periods/{academicPeriod}/begin-closing', ['App\Http\Controllers\AcademicPeriodController', 'beginClosing'])->name('academic-periods.begin-closing');
            Route::post('academic-periods/{academicPeriod}/reopen', ['App\Http\Controllers\AcademicPeriodController', 'reopen'])->name('academic-periods.reopen');

            Route::middleware(['App\Http\Middleware\EnsureAcademicPeriodIsSet'])->group(function () {
                // fee categories routes
                Route::resource('fees/fee-categories', FeeCategoryController::class);

                // fee invoice record routes
                Route::resource('fees/fee-invoices/fee-invoice-records', FeeInvoiceRecordController::class);

                // shared facility routes
                Route::resource('facilities', FacilityController::class)->only(['index', 'store', 'update', 'destroy']);
                Route::post('facilities/bookings', [FacilityController::class, 'book'])->name('facilities.book');
                Route::delete('facilities/bookings/{facility_booking}', [FacilityController::class, 'cancelBooking'])->name('facilities.bookings.cancel');

                // boarding routes
                Route::middleware('feature:boarding')->group(function (): void {
                    Route::resource('boarding/houses', DormitoryController::class)
                        ->only(['index', 'create', 'store', 'show'])
                        ->parameters(['houses' => 'dormitory'])
                        ->names('dormitories');
                    Route::post('boarding/places', [BoardingPlaceController::class, 'store'])->name('boarding-places.store');
                    Route::delete('boarding/places/{student_record}', [BoardingPlaceController::class, 'destroy'])->name('boarding-places.destroy');
                    Route::get('boarding/nights-away', [OvernightLeaveController::class, 'index'])->name('overnight-leaves.index');
                    Route::post('boarding/nights-away', [OvernightLeaveController::class, 'store'])->name('overnight-leaves.store');
                    Route::put('boarding/nights-away/{overnight_leave}', [OvernightLeaveController::class, 'update'])->name('overnight-leaves.update');
                });

                // library routes
                Route::middleware('feature:library')->group(function (): void {
                    Route::get('library', [LibraryCopyController::class, 'index'])->name('library-copies.index');
                    Route::post('library', [LibraryCopyController::class, 'store'])->name('library-copies.store');
                    Route::delete('library/copies/{library_copy}', [LibraryCopyController::class, 'destroy'])->name('library-copies.destroy');
                    Route::get('library/desk', [LibraryLoanController::class, 'index'])->name('library-loans.index');
                    Route::post('library/desk', [LibraryLoanController::class, 'store'])->name('library-loans.store');
                    Route::post('library/desk/section', [LibraryLoanController::class, 'storeForSection'])->name('library-loans.section.store');
                    Route::put('library/desk/{library_loan}', [LibraryLoanController::class, 'update'])->name('library-loans.update');
                    Route::get('library/queue', [LibraryReservationController::class, 'index'])->name('library-reservations.index');
                    Route::post('library/queue', [LibraryReservationController::class, 'store'])->name('library-reservations.store');
                    Route::delete('library/queue/{library_reservation}', [LibraryReservationController::class, 'destroy'])->name('library-reservations.destroy');
                    Route::get('library/rules', [LibraryLendingRulesController::class, 'edit'])->name('library-rules.edit');
                    Route::put('library/rules', [LibraryLendingRulesController::class, 'update'])->name('library-rules.update');
                });

                // budget routes
                Route::resource('fees/budgets', BudgetController::class)->only(['index', 'store', 'destroy']);

                // student account routes
                Route::get('fees/accounts/{student_record}', [StudentAccountController::class, 'show'])->name('student-accounts.show');
                Route::post('fees/accounts/{student_record}/credit', [StudentAccountController::class, 'applyCredit'])->name('student-accounts.apply-credit');
                Route::post('fees/accounts/{student_record}/refund', [StudentAccountController::class, 'refund'])->name('student-accounts.refund');
                Route::post('fees/payments/{student_payment}/reverse', [StudentAccountController::class, 'reverse'])->name('student-payments.reverse');

                // fee incvoice routes
                Route::get('fees/fee-invoices/{fee_invoice}/pay', ['App\Http\Controllers\FeeInvoiceController', 'payView'])->name('fee-invoices.pay');
                Route::post('fees/fee-invoices/{fee_invoice}/pay', ['App\Http\Controllers\FeeInvoiceController', 'pay'])->name('fee-invoices.pay.store');
                Route::get('fees/fee-invoices/{fee_invoice}/print', ['App\Http\Controllers\FeeInvoiceController', 'print'])->name('fee-invoices.print');
                Route::resource('fees/fee-invoices', FeeInvoiceController::class);

                // fee routes
                Route::resource('fees', FeeController::class);

                Route::post('syllabi/{syllabus}/revise', [SyllabusController::class, 'revise'])->name('syllabi.revise');
                Route::post('syllabi/{syllabus}/publish', [SyllabusController::class, 'publish'])->name('syllabi.publish');
                Route::resource('syllabi', SyllabusController::class);

                // timetable route
                Route::post('timetables/{timetable}/section-overrides', [TimetableController::class, 'createSectionOverride'])->name('timetables.section-overrides.store');
                Route::post('timetables/{timetable}/substitutions', [TimetableController::class, 'storeSubstitution'])->name('timetables.substitutions.store');
                Route::resource('timetables', TimetableController::class);
                Route::resource('custom-timetable-items', CustomTimetableItemController::class);

                // manage timetable
                Route::get('timetables/{timetable}/manage', ['App\Http\Controllers\TimetableController', 'manage'])->name('timetables.manage');
                Route::get('timetables/{timetable}/print', ['App\Http\Controllers\TimetableController', 'print'])->name('timetables.print');
                Route::post('timetables/{timetable}/publish', ['App\Http\Controllers\TimetableController', 'publish'])->name('timetables.publish');
                Route::post('timetables/{timetable}/revise', ['App\Http\Controllers\TimetableController', 'revise'])->name('timetables.revise');

                // timetable-timeslot route
                Route::resource('timetables/manage/time-slots', TimetableTimeSlotController::class);
                Route::post('timetables/manage/time-slots/{time_slot}/record/create', ['App\Http\Controllers\TimetableTimeSlotController', 'addTimetableRecord'])->name('timetables.records.create')->scopeBindings();

                // set exam status
                Route::post('exams/{exam}/set--active-status', ['App\Http\Controllers\ExamController', 'setExamActiveStatus'])->name('exams.set-active-status');

                // exam routes
                Route::resource('exams', ExamController::class);

                // exam slot routes
                Route::scopeBindings()->group(function () {
                    Route::resource('exams/{exam}/manage/exam-slots', ExamSlotController::class);
                });
            });
        });

        // student routes
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/print', ['App\Http\Controllers\StudentController', 'printProfile'])->name('students.print-profile')->withoutMiddleware(['App\Http\Middleware\PreventGraduatedStudent']);

        // admin routes
        Route::resource('admins', AdminController::class);

        // teacher routes
        Route::resource('teachers', TeacherController::class);

        // parent routes
        Route::resource('parents', ParentController::class);
        Route::get('parents/{parent}/assign-student-to-parent', ['App\Http\Controllers\ParentController', 'assignStudentsView'])->name('parents.assign-student');
        Route::post('parents/{parent}/assign-student-to-parent', ['App\Http\Controllers\ParentController', 'assignStudent']);

        // account access routes
        Route::get('users/invitations', ['App\Http\Controllers\AccountInvitationController', 'index'])->name('users.invitations.index');
        Route::post('users/{user}/account-status', ['App\Http\Controllers\AccountStatusController', 'update'])->name('users.account-status');
        Route::post('users/{user}/invitation', ['App\Http\Controllers\AccountInvitationController', 'send'])->name('users.invitation.send');
        Route::delete('users/{user}/invitation', ['App\Http\Controllers\AccountInvitationController', 'revoke'])->name('users.invitation.revoke');

        // academic year routes
        Route::resource('academic-years', AcademicYearController::class)->except(['store', 'update']);
        Route::post('academic-years/set', ['App\Http\Controllers\AcademicYearController', 'setAcademicYear'])->name('academic-years.set-academic-year');
        Route::post('academic-years/{academic_year}/close', ['App\Http\Controllers\AcademicYearController', 'close'])->name('academic-years.close');
        Route::post('academic-years/{academic_year}/begin-closing', ['App\Http\Controllers\AcademicYearController', 'beginClosing'])->name('academic-years.begin-closing');
        Route::post('academic-years/{academic_year}/reopen', ['App\Http\Controllers\AcademicYearController', 'reopen'])->name('academic-years.reopen');
        Route::get('academic-years/{academic_year}/instructional-model', ['App\Http\Controllers\InstructionalModelController', 'edit'])->name('academic-years.instructional-model.edit');
        Route::put('academic-years/{academic_year}/instructional-model', ['App\Http\Controllers\InstructionalModelController', 'update'])->name('academic-years.instructional-model.update');
        Route::post('academic-years/{academic_year}/instructional-model/migration', ['App\Http\Controllers\InstructionalModelController', 'migrate'])->name('academic-years.instructional-model.migrate');
        Route::post('academic-years/{academic_year}/instructional-model/exceptions', ['App\Http\Controllers\InstructionalModelController', 'grantException'])->name('academic-years.instructional-model.exceptions.store');
        Route::delete('academic-years/{academic_year}/instructional-model/exceptions/{exception}', ['App\Http\Controllers\InstructionalModelController', 'revokeException'])->name('academic-years.instructional-model.exceptions.destroy');

        // subject routes
        Route::resource('subjects', SubjectController::class);

        // notice routes
        Route::resource('notices', NoticeController::class);
    });
});
