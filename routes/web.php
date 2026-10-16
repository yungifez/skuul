<?php

use App\Http\Controllers\AcademicCycleSectionController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AcademicYearSetupController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmissionWaitlistController;
use App\Http\Controllers\BoardingRollController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CalendarTemplateController;
use App\Http\Controllers\CashDepositController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\CustomTimetableItemController;
use App\Http\Controllers\DormitoryController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FeeInvoiceController;
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
use App\Http\Controllers\OrganizationBoardingResidenceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationDashboardController;
use App\Http\Controllers\OvernightLeaveController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PortalGraduationController;
use App\Http\Controllers\PortalInvoicesController;
use App\Http\Controllers\PortalProgramController;
use App\Http\Controllers\PortalResultController;
use App\Http\Controllers\PortalTimetableController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolSetupController;
use App\Http\Controllers\StudentAccountController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use App\Http\Middleware\ResolveDomainContext;
use App\Http\Middleware\SetActiveAcademicPeriod;
use App\Http\Middleware\SetActiveSchool;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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

Route::middleware('auth', 'verified', 'App\Http\Middleware\EnsureAccountIsActive', 'App\Http\Middleware\EnsurePasswordChange', 'App\Http\Middleware\PreventGraduatedStudent')->prefix('dashboard')->namespace('App\Http\Controllers')->group(function () {
    // Families use portal authorization, not a staff working-school membership.
    Route::withoutMiddleware(['App\Http\Middleware\PreventGraduatedStudent'])->group(function (): void {
        Route::get('portal/overview', ['App\Http\Controllers\PortalOverviewController', 'index'])->name('portal.overview');
        Route::get('portal/notification-preferences', [NoticeNotificationPreferenceController::class, 'portalEdit'])->name('portal.notification-preferences.edit');
        Route::get('portal/enrollments/{studentRecord}/results', [PortalResultController::class, 'index'])->name('portal.results.index');
        Route::get('portal/enrollments/{studentRecord}/timetable', [PortalTimetableController::class, 'show'])->name('portal.timetable.show');
        Route::get('portal/enrollments/{studentRecord}/attendance', ['App\Http\Controllers\PortalAttendanceController', 'show'])->name('portal.attendance.show');
        Route::get('portal/enrollments/{studentRecord}/calendar', ['App\Http\Controllers\PortalCalendarController', 'index'])->name('portal.calendar.index');
        Route::get('portal/enrollments/{studentRecord}/invoices', [PortalInvoicesController::class, 'index'])->name('portal.invoices.index');
        Route::get('portal/enrollments/{studentRecord}/documents', ['App\Http\Controllers\PortalDocumentsController', 'index'])->name('portal.documents.index');
        Route::get('portal/enrollments/{studentRecord}/documents/report-cards/{reportCardSnapshot}', ['App\Http\Controllers\PortalDocumentsController', 'downloadReportCard'])->name('portal.documents.report-cards.download');
        Route::get('portal/enrollments/{studentRecord}/documents/transcripts/{transcriptSnapshot}', ['App\Http\Controllers\PortalDocumentsController', 'downloadTranscript'])->name('portal.documents.transcripts.download');
        Route::get('portal/enrollments/{studentRecord}/boarding', ['App\Http\Controllers\PortalBoardingController', 'index'])->name('portal.boarding.index');
        Route::get('portal/enrollments/{studentRecord}/notices', ['App\Http\Controllers\PortalNoticeController', 'index'])->name('portal.notices.index');
        Route::get('portal/enrollments/{studentRecord}/library', ['App\Http\Controllers\PortalLibraryController', 'index'])->name('portal.library.index');
        Route::get('portal/enrollments/{studentRecord}/requests', ['App\Http\Controllers\PortalRequestController', 'index'])->name('portal.requests.index');
        Route::get('portal/enrollments/{studentRecord}/graduation', [PortalGraduationController::class, 'show'])->name('portal.graduation.show');
        Route::get('portal/enrollments/{studentRecord}/programmes', [PortalProgramController::class, 'index'])->name('portal.programmes.index');
        Route::get('portal/enrollments/{studentRecord}/syllabi', ['App\Http\Controllers\PortalSyllabusController', 'index'])->name('portal.syllabi.index');
        Route::get('notices/{notice}/attachment', NoticeAttachmentController::class)->name('notices.attachments.download');
    });
    Route::get('notice-preferences', [NoticeNotificationPreferenceController::class, 'edit'])->middleware('App\Http\Middleware\RequireActiveSchool')->name('notice-preferences.edit');
    // Organization administration is separate from working-school access.
    Route::get('organizations/{organization}/members', ['App\Http\Controllers\OrganizationMemberController', 'index'])->name('organizations.members.index');
    Route::get('organizations/{organization}/domains', ['App\Http\Controllers\OrganizationDomainController', 'index'])->name('organizations.domains.index');
    Route::get('organizations/{organization}/billing-groups', ['App\Http\Controllers\OrganizationBillingGroupController', 'index'])->name('organizations.billing-groups.index');
    Route::get('organizations/{organization}/boarding-residences', [OrganizationBoardingResidenceController::class, 'index'])->name('organizations.boarding-residences.index');
    Route::resource('organizations', OrganizationController::class)->only(['index', 'create', 'show', 'edit']);
    Route::get('organizations/{organization}/dashboard', OrganizationDashboardController::class)->name('organizations.dashboard');
    Route::resource('organizations.calendar-templates', CalendarTemplateController::class)
        ->parameters(['calendar-templates' => 'calendarTemplate'])
        ->only(['index', 'create', 'edit']);

    // Campus role management requires a working school.
    Route::middleware('App\Http\Middleware\RequireActiveSchool')->group(function () {
        Route::get('roles', ['App\Http\Controllers\CampusRoleController', 'index'])->name('roles.index');
        Route::get('roles/create', ['App\Http\Controllers\CampusRoleController', 'create'])->name('roles.create');
        Route::get('roles/{role}/edit', ['App\Http\Controllers\CampusRoleController', 'edit'])->name('roles.edit');
    });

    Route::get('schools/settings', ['App\Http\Controllers\SchoolController', 'settings'])->name('schools.settings')->middleware('App\Http\Middleware\RequireActiveSchool');
    Route::view('schools/operating-profile', 'pages.school.operating-profile')->name('schools.operating-profile.edit')->middleware('App\Http\Middleware\RequireActiveSchool');
    Route::view('schools/features', 'pages.school.features')->name('schools.features.edit')->middleware('App\Http\Middleware\RequireActiveSchool');

    Route::get('schools/{school}/setup/{step?}', [SchoolSetupController::class, 'show'])->name('schools.setup');
    Route::resource('schools', SchoolController::class);

    // Operational routes require a working school, including for platform administrators.
    Route::middleware(['App\Http\Middleware\RequireActiveSchool'])->group(function () {
        Route::resource('grading-scales', GradingScaleController::class)
            ->parameters(['grading-scales' => 'gradingScale'])
            ->only(['index']);

        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard')->withoutMiddleware(['App\Http\Middleware\PreventGraduatedStudent']);

        Route::resource('academic-levels', AcademicLevelController::class)
            ->parameters(['academic-levels' => 'academicLevel'])
            ->only(['index', 'create', 'show', 'edit']);

        // Register the literal path before the resource wildcard.
        Route::get('academic-cycle-sections/roll-forward', [AcademicCycleSectionController::class, 'rollForwardForm'])
            ->name('academic-cycle-sections.roll-forward.show');
        Route::resource('academic-cycle-sections', AcademicCycleSectionController::class)
            ->parameters(['academic-cycle-sections' => 'academicCycleSection'])
            ->only(['index', 'create', 'show', 'edit']);

        Route::get('admissions/waitlist', [AdmissionWaitlistController::class, 'index'])->name('admissions.waitlist.index');
        Route::post('admissions/waitlist', [AdmissionWaitlistController::class, 'store'])->name('admissions.waitlist.store');
        Route::post('admissions/waitlist/{admissionWaitlistEntry}/offer', [AdmissionWaitlistController::class, 'offer'])->name('admissions.waitlist.offer');
        Route::post('admissions/waitlist/{admissionWaitlistEntry}/accept', [AdmissionWaitlistController::class, 'accept'])->name('admissions.waitlist.accept');
        Route::post('admissions/waitlist/{admissionWaitlistEntry}/decline', [AdmissionWaitlistController::class, 'decline'])->name('admissions.waitlist.decline');

        // Reports select their own period and do not require a working period.
        Route::get('reports', ['App\Http\Controllers\ReportController', 'index'])->name('reports.index');
        Route::get('report-cards', ['App\Http\Controllers\ReportCardController', 'index'])->name('report-cards.index');
        Route::get('report-cards/{reportCardSnapshot}', ['App\Http\Controllers\ReportCardController', 'show'])->name('report-cards.show');
        Route::get('transcripts', ['App\Http\Controllers\TranscriptController', 'index'])->name('transcripts.index');

        Route::middleware(['feature:attendance'])->group(function () {
            Route::get('attendance/register', ['App\Http\Controllers\AttendanceRegisterController', 'index'])->name('attendance.register');
            Route::post('attendance/register', ['App\Http\Controllers\AttendanceRegisterController', 'store'])->name('attendance.register.store');
        });
        Route::get('reports/{reportRun}/download', ['App\Http\Controllers\ReportController', 'download'])->name('reports.download');

        // Incidents use occurrence dates rather than the working academic period.
        Route::middleware(['feature:discipline'])->group(function () {
            Route::get('incidents', ['App\Http\Controllers\IncidentController', 'index'])->name('incidents.index');
            Route::get('incidents/create', ['App\Http\Controllers\IncidentController', 'create'])->name('incidents.create');
            Route::get('incidents/{incident}', ['App\Http\Controllers\IncidentController', 'show'])->name('incidents.show');
        });

        // Support plans span periods. Health records require separate access from student profiles.
        Route::middleware(['feature:wellbeing'])->group(function () {
            Route::get('support-plans', ['App\Http\Controllers\SupportPlanController', 'index'])->name('support-plans.index');
            Route::get('support-plans/create', ['App\Http\Controllers\SupportPlanController', 'create'])->name('support-plans.create');
            Route::get('support-plans/{supportPlan}', ['App\Http\Controllers\SupportPlanController', 'show'])->name('support-plans.show');

            Route::get('health-records', ['App\Http\Controllers\StudentHealthRecordController', 'index'])->name('health-records.index');
            Route::get('health-records/{studentRecord}', ['App\Http\Controllers\StudentHealthRecordController', 'edit'])->name('health-records.edit');
        });

        // Employment records do not depend on an academic period.
        Route::middleware(['feature:staff_operations'])->group(function () {
            Route::get('staff-profiles', ['App\Http\Controllers\StaffProfileController', 'index'])->name('staff-profiles.index');
            Route::get('staff-profiles/create', ['App\Http\Controllers\StaffProfileController', 'create'])->name('staff-profiles.create');
            Route::get('staff-profiles/{staffProfile}', ['App\Http\Controllers\StaffProfileController', 'show'])->name('staff-profiles.show');

            Route::get('staff-leave', ['App\Http\Controllers\StaffLeaveRequestController', 'index'])->name('staff-leave.index');
        });

        // Both participating schools can read sharing requests; policies scope access.
        Route::get('data-sharing-requests', ['App\Http\Controllers\DataSharingRequestController', 'index'])->name('data-sharing-requests.index');
        Route::get('data-sharing-requests/create', ['App\Http\Controllers\DataSharingRequestController', 'create'])->name('data-sharing-requests.create');
        Route::get('data-sharing-requests/{dataSharingRequest}', ['App\Http\Controllers\DataSharingRequestController', 'show'])->name('data-sharing-requests.show');

        // Keep the staff inbox available after the family portal closes.
        Route::get('portal-requests', ['App\Http\Controllers\PortalRequestController', 'inbox'])->name('portal-requests.index');

        Route::middleware(['feature:graduation_plans'])->group(function (): void {
            Route::get('graduation-plans', ['App\Http\Controllers\GraduationPlanController', 'index'])->name('graduation-plans.index');
            Route::get('graduation-plans/create', ['App\Http\Controllers\GraduationPlanController', 'create'])->name('graduation-plans.create');
            Route::get('graduation-plans/{graduationPlan}', ['App\Http\Controllers\GraduationPlanController', 'show'])->name('graduation-plans.show');
        });

        Route::middleware(['feature:ranking'])->group(function () {
            Route::get('rankings', ['App\Http\Controllers\RankingController', 'index'])->name('rankings.index');
        });

        // Calendar events select their own dates rather than the working academic period.
        Route::middleware(['feature:events'])->group(function () {
            Route::get('calendar-events', ['App\Http\Controllers\CalendarEventController', 'index'])->name('calendar-events.index');
            Route::get('calendar-events/create', ['App\Http\Controllers\CalendarEventController', 'create'])->name('calendar-events.create');
            Route::get('calendar-events/{calendarEvent}', ['App\Http\Controllers\CalendarEventController', 'edit'])->name('calendar-events.edit');
        });

        // Cohorts and programmes can span academic periods.
        Route::get('cohorts', ['App\Http\Controllers\CohortController', 'index'])->name('cohorts.index');
        Route::get('cohorts/create', ['App\Http\Controllers\CohortController', 'create'])->name('cohorts.create');
        Route::get('cohorts/{cohort}', ['App\Http\Controllers\CohortController', 'show'])->name('cohorts.show');

        Route::middleware(['feature:programmes'])->group(function (): void {
            Route::get('programs', ['App\Http\Controllers\ProgramController', 'index'])->name('programs.index');
            Route::get('programs/create', ['App\Http\Controllers\ProgramController', 'create'])->name('programs.create');
            Route::get('programs/{program}', ['App\Http\Controllers\ProgramController', 'show'])->name('programs.show');
        });

        // Imports use the working school and select their own academic context.
        Route::middleware(['feature:imports'])->group(function () {
            Route::get('imports', ['App\Http\Controllers\ImportController', 'index'])->name('imports.index');
            Route::get('imports/{importBatch}', ['App\Http\Controllers\ImportController', 'show'])->name('imports.show');
        });

        // Exam planning selects its own calendar, including draft years.
        Route::post('exams/{exam}/set--active-status', ['App\Http\Controllers\ExamController', 'setExamActiveStatus'])->name('exams.set-active-status');
        Route::resource('exams', ExamController::class)->except(['show', 'store', 'update', 'destroy']);

        Route::middleware(['App\Http\Middleware\EnsureAcademicYearIsSet', 'App\Http\Middleware\CreateCurrentAcademicYearRecord'])->group(function () {
            Route::get('gradebooks', [GradebookController::class, 'index'])
                ->middleware('App\Http\Middleware\EnsureAcademicPeriodIsSet')
                ->name('gradebooks.index');
            Route::get('course-offerings/bulk-create', [CourseOfferingController::class, 'bulkCreate'])->name('course-offerings.bulk-create');
            Route::get('course-offerings/bulk-create/form', [CourseOfferingController::class, 'bulkCreateForm'])->name('course-offerings.bulk-create.form');
            Route::get('course-offerings/roll-forward', [CourseOfferingController::class, 'rollForwardForm'])->name('course-offerings.roll-forward.show');
            Route::resource('course-offerings', CourseOfferingController::class)
                ->only(['index', 'create', 'edit'])
                ->parameters(['course-offerings' => 'courseOffering']);
            Route::get('course-offerings/{courseOffering}/gradebook', [GradebookController::class, 'show'])->name('course-offerings.gradebook.show');

            Route::get('students/promotions', ['App\Http\Controllers\PromotionController', 'index'])->name('students.promotions');
            Route::get('students/promote', ['App\Http\Controllers\PromotionController', 'promoteView'])->name('students.promote');
            Route::get('students/promotions/{promotion}', ['App\Http\Controllers\PromotionController', 'show'])->name('students.promotions.show');

            Route::get('students/campus-moves', ['App\Http\Controllers\CampusMoveRequestController', 'index'])->name('campus-moves.index');

            Route::get('students/graduations', ['App\Http\Controllers\GraduationController', 'index'])->name('students.graduations');
            Route::get('students/graduate', ['App\Http\Controllers\GraduationController', 'graduateView'])->name('students.graduate');

            Route::get('academic-periods', [AcademicPeriodController::class, 'index'])->name('academic-periods.index');
            Route::get('academic-periods/create', [AcademicPeriodController::class, 'create'])->name('academic-periods.create');
            Route::post('academic-periods', [AcademicPeriodController::class, 'store'])->name('academic-periods.store');
            Route::get('academic-periods/{academicPeriod}/edit', [AcademicPeriodController::class, 'edit'])->name('academic-periods.edit');
            Route::put('academic-periods/{academicPeriod}', [AcademicPeriodController::class, 'update'])->name('academic-periods.update');

            // Finance uses financial periods independently of the working teaching term.
            Route::resource('fees/fee-categories', FeeCategoryController::class)->except(['store', 'update', 'destroy']);
            Route::resource('fees/expenses', ExpenseController::class)->only(['index', 'create']);
            Route::resource('fees/cash-deposits', CashDepositController::class)->only(['index', 'create']);
            Route::get('fees/accounts/{student_record}', [StudentAccountController::class, 'show'])->name('student-accounts.show');
            Route::get('fees/payments/{student_payment}/receipt', [StudentPaymentController::class, 'print'])->name('student-payments.receipt');
            Route::get('fees/fee-invoices/{fee_invoice}/pay', [FeeInvoiceController::class, 'payView'])->name('fee-invoices.pay');
            Route::get('fees/fee-invoices/{fee_invoice}/print', [FeeInvoiceController::class, 'print'])->name('fee-invoices.print');
            Route::resource('fees/fee-invoices', FeeInvoiceController::class)->except(['store', 'update', 'destroy']);
            Route::get('fees/budgets', [BudgetController::class, 'index'])->name('budgets.index');
            Route::resource('fees', FeeController::class)->except(['store', 'update', 'destroy']);

            Route::middleware(['App\Http\Middleware\EnsureAcademicPeriodIsSet'])->group(function () {

                Route::get('facilities', [FacilityController::class, 'index'])->name('facilities.index');

                Route::middleware('feature:boarding')->group(function (): void {
                    Route::resource('boarding/houses', DormitoryController::class)
                        ->only(['index', 'create', 'show', 'edit'])
                        ->parameters(['houses' => 'dormitory'])
                        ->names('dormitories');
                    Route::get('boarding/nights-away', [OvernightLeaveController::class, 'index'])->name('overnight-leaves.index');
                    Route::get('boarding/rolls', [BoardingRollController::class, 'index'])->name('boarding-rolls.index');
                    Route::get('boarding/rolls/{boardingRoll}', [BoardingRollController::class, 'show'])->name('boarding-rolls.show');
                });

                Route::middleware('feature:library')->group(function (): void {
                    Route::get('library', [LibraryCopyController::class, 'index'])->name('library-copies.index');
                    Route::get('library/desk', [LibraryLoanController::class, 'index'])->name('library-loans.index');
                    Route::get('library/queue', [LibraryReservationController::class, 'index'])->name('library-reservations.index');
                    Route::get('library/rules', [LibraryLendingRulesController::class, 'edit'])->name('library-rules.edit');
                });

                Route::get('syllabi/coverage', [SyllabusController::class, 'coverage'])->name('syllabi.coverage');
                Route::get('syllabi/library', [SyllabusController::class, 'library'])->name('syllabi.library');
                Route::get('syllabi/{syllabus}/lesson-notes', [SyllabusController::class, 'lessonNotes'])->name('syllabi.lesson-notes');
                Route::get('syllabi/{syllabus}/print', [SyllabusController::class, 'print'])->name('syllabi.print');
                Route::resource('syllabi', SyllabusController::class);

                Route::resource('timetables', TimetableController::class)->except(['store', 'update']);
                Route::resource('custom-timetable-items', CustomTimetableItemController::class)->except(['store', 'update', 'destroy']);

                Route::get('timetables/{timetable}/manage', ['App\Http\Controllers\TimetableController', 'manage'])->name('timetables.manage');
                Route::get('timetables/{timetable}/print', ['App\Http\Controllers\TimetableController', 'print'])->name('timetables.print');

            });
        });

        Route::resource('students', StudentController::class)->except(['store', 'update', 'destroy']);
        Route::get('students/{student}/print', ['App\Http\Controllers\StudentController', 'printProfile'])->name('students.print-profile')->withoutMiddleware(['App\Http\Middleware\PreventGraduatedStudent']);

        Route::resource('admins', AdminController::class)->except(['store', 'update', 'destroy']);

        Route::resource('teachers', TeacherController::class)->except(['store', 'update', 'destroy']);

        Route::resource('parents', ParentController::class)->except(['store', 'update', 'destroy']);
        Route::get('parents/{parent}/assign-student-to-parent', ['App\Http\Controllers\ParentController', 'assignStudentsView'])->name('parents.assign-student');

        Route::get('users/invitations', ['App\Http\Controllers\AccountInvitationController', 'index'])->name('users.invitations.index');

        Route::get('academic-years/{academic_year}/setup/{step?}', [AcademicYearSetupController::class, 'show'])->name('academic-years.setup');
        Route::resource('academic-years', AcademicYearController::class)->except(['store', 'update']);
        Route::get('academic-years/{academic_year}/instructional-model', ['App\Http\Controllers\InstructionalModelController', 'edit'])->name('academic-years.instructional-model.edit');

        Route::resource('subjects', SubjectController::class)->except(['store', 'update', 'destroy']);

        Route::resource('notices', NoticeController::class)->only(['index', 'create', 'show']);
    });
});
