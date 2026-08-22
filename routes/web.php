<?php

use App\Http\Controllers\AcademicCycleController;
use App\Http\Controllers\AcademicCycleSectionController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\CalendarTemplateController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

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
    // Organization administration is separate from working-school access.
    Route::get('organizations/{organization}/members', ['App\Http\Controllers\OrganizationMemberController', 'index'])->name('organizations.members.index');
    Route::resource('organizations', OrganizationController::class)->except('destroy');
    Route::get('organizations/{organization}/dashboard', OrganizationDashboardController::class)->name('organizations.dashboard');
    Route::resource('organizations.calendar-templates', CalendarTemplateController::class)
        ->parameters(['calendar-templates' => 'calendarTemplate'])
        ->except(['show', 'destroy']);
    Route::post('organizations/{organization}/calendar-templates/{calendarTemplate}/cycles', [AcademicCycleController::class, 'store'])->name('organizations.calendar-templates.cycles.store');
    Route::post('organizations/{organization}/calendar-templates/{calendarTemplate}/campuses/{school}', [CalendarTemplateController::class, 'overrideCampus'])->name('organizations.calendar-templates.campuses.override');
    Route::delete('organizations/{organization}/calendar-templates/{calendarTemplate}/campuses/{school}', [CalendarTemplateController::class, 'inheritCampus'])->name('organizations.calendar-templates.campuses.inherit');

    // manage school settings
    Route::get('schools/settings', ['App\Http\Controllers\SchoolController', 'settings'])->name('schools.settings')->middleware('App\Http\Middleware\RequireActiveSchool');

    // School routes
    Route::resource('schools', SchoolController::class);
    Route::post('schools/set-school', ['App\Http\Controllers\SchoolController', 'setSchool'])->name('schools.setSchool');

    // super admin must have school id set
    Route::middleware(['App\Http\Middleware\RequireActiveSchool'])->group(function () {
        // dashboard route
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard')->withoutMiddleware(['App\Http\Middleware\PreventGraduatedStudent']);

        // class routes
        Route::resource('classes', MyClassController::class);

        // class groups routes
        Route::resource('class-groups', ClassGroupController::class);

        // sections routes
        Route::resource('sections', SectionController::class);

        // New curriculum structure. These records are cycle-specific and do
        // not replace existing student placement or section history yet.
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

        // report routes. A report reads whatever period it is given, so it
        // does not need one to be set first.
        Route::post('reports', ['App\Http\Controllers\ReportController', 'store'])->name('reports.store');
        Route::get('reports/{reportRun}/download', ['App\Http\Controllers\ReportController', 'download'])->name('reports.download');

        // import routes. An import reads the school, not the period, so it
        // does not need a period to be set first. A school that turned imports
        // off closes the way in without losing what it already imported.
        Route::middleware(['feature:imports'])->group(function () {
            Route::post('imports', ['App\Http\Controllers\ImportController', 'store'])->name('imports.store');
            Route::post('imports/{importBatch}/apply', ['App\Http\Controllers\ImportController', 'apply'])->name('imports.apply');
            Route::post('imports/{importBatch}/cancel', ['App\Http\Controllers\ImportController', 'cancel'])->name('imports.cancel');
        });

        Route::middleware(['App\Http\Middleware\EnsureAcademicYearIsSet', 'App\Http\Middleware\CreateCurrentAcademicYearRecord'])->group(function () {
            Route::resource('course-offerings', CourseOfferingController::class)->only(['index', 'create', 'store']);
            Route::post('course-offerings/{courseOffering}/activate', ['App\Http\Controllers\CourseOfferingController', 'activate'])->name('course-offerings.activate');
            Route::post('course-offerings/{courseOffering}/teachers', ['App\Http\Controllers\CourseOfferingController', 'assignTeacher'])->name('course-offerings.teachers.store');

            // promotion routes
            Route::get('students/promotions', ['App\Http\Controllers\PromotionController', 'index'])->name('students.promotions');
            Route::get('students/promote', ['App\Http\Controllers\PromotionController', 'promoteView'])->name('students.promote');
            Route::post('students/promote', ['App\Http\Controllers\PromotionController', 'promote']);
            Route::get('students/promotions/{promotion}', ['App\Http\Controllers\PromotionController', 'show'])->name('students.promotions.show');
            Route::delete('students/promotions/{promotion}/reset', ['App\Http\Controllers\PromotionController', 'resetPromotion'])->name('students.promotions.reset');

            // graduation routes
            Route::get('students/graduations', ['App\Http\Controllers\GraduationController', 'index'])->name('students.graduations');
            Route::get('students/graduate', ['App\Http\Controllers\GraduationController', 'graduateView'])->name('students.graduate');
            Route::post('students/graduate', ['App\Http\Controllers\GraduationController', 'graduate']);
            Route::delete('students/graduations/{student}/reset', ['App\Http\Controllers\GraduationController', 'resetGraduation'])->name('students.graduations.reset');

            // academic period routes
            Route::resource('academic-periods', AcademicPeriodController::class)->parameters(['academic-periods' => 'academicPeriod']);
            Route::post('academic-periods/set', ['App\Http\Controllers\AcademicPeriodController', 'setAcademicPeriod'])->name('academic-periods.set-academic-period');
            Route::post('academic-periods/{academicPeriod}/close', ['App\Http\Controllers\AcademicPeriodController', 'close'])->name('academic-periods.close');
            Route::post('academic-periods/{academicPeriod}/begin-closing', ['App\Http\Controllers\AcademicPeriodController', 'beginClosing'])->name('academic-periods.begin-closing');
            Route::post('academic-periods/{academicPeriod}/reopen', ['App\Http\Controllers\AcademicPeriodController', 'reopen'])->name('academic-periods.reopen');

            Route::middleware(['App\Http\Middleware\EnsureAcademicPeriodIsSet'])->group(function () {
                // fee categories routes
                Route::resource('fees/fee-categories', FeeCategoryController::class);

                // fee invoice record routes
                Route::post('fees/fee-invoices/fee-invoice-records/{fee_invoice_record}/pay', ['App\Http\Controllers\FeeInvoiceRecordController', 'pay'])->name('fee-invoices-records.pay');
                Route::resource('fees/fee-invoices/fee-invoice-records', FeeInvoiceRecordController::class);

                // fee incvoice routes
                Route::get('fees/fee-invoices/{fee_invoice}/pay', ['App\Http\Controllers\FeeInvoiceController', 'payView'])->name('fee-invoices.pay');
                Route::get('fees/fee-invoices/{fee_invoice}/print', ['App\Http\Controllers\FeeInvoiceController', 'print'])->name('fee-invoices.print');
                Route::resource('fees/fee-invoices', FeeInvoiceController::class);

                // fee routes
                Route::resource('fees', FeeController::class);

                // syllabi route
                Route::resource('syllabi', SyllabusController::class);

                // timetable route
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

                // set publish result status
                Route::post('exams/{exam}/set-publish-result-status', ['App\Http\Controllers\ExamController', 'setPublishResultStatus'])->name('exams.set-publish-result-status');
                // manage exam record
                Route::resource('exams/exam-records', ExamRecordController::class);

                // exam tabulation sheet
                Route::get('exams/tabulation-sheet', ['App\Http\Controllers\ExamController', 'examTabulation'])->name('exams.tabulation');

                // result tabulation sheet
                Route::get('exams/academic-period-result-tabulation', ['App\Http\Controllers\ExamController', 'academicPeriodResultTabulation'])->name('exams.academic-period-result-tabulation');
                Route::get('exams/academic-year-result-tabulation', ['App\Http\Controllers\ExamController', 'academicYearResultTabulation'])->name('exams.academic-year-result-tabulation');

                // result checker
                Route::get('exams/result-checker', ['App\Http\Controllers\ExamController', 'resultChecker'])->name('exams.result-checker');

                // exam routes
                Route::resource('exams', ExamController::class);

                // exam slot routes
                Route::scopeBindings()->group(function () {
                    Route::resource('exams/{exam}/manage/exam-slots', ExamSlotController::class);
                });

                // grade system routes
                Route::resource('grade-systems', GradeSystemController::class);
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
        Route::resource('academic-years', AcademicYearController::class);
        Route::post('academic-years/set', ['App\Http\Controllers\AcademicYearController', 'setAcademicYear'])->name('academic-years.set-academic-year');
        Route::post('academic-years/{academic_year}/close', ['App\Http\Controllers\AcademicYearController', 'close'])->name('academic-years.close');
        Route::post('academic-years/{academic_year}/begin-closing', ['App\Http\Controllers\AcademicYearController', 'beginClosing'])->name('academic-years.begin-closing');
        Route::post('academic-years/{academic_year}/reopen', ['App\Http\Controllers\AcademicYearController', 'reopen'])->name('academic-years.reopen');
        Route::get('academic-years/{academic_year}/instructional-model', ['App\Http\Controllers\InstructionalModelController', 'edit'])->name('academic-years.instructional-model.edit');
        Route::put('academic-years/{academic_year}/instructional-model', ['App\Http\Controllers\InstructionalModelController', 'update'])->name('academic-years.instructional-model.update');

        // assign teachers to subject in class
        Route::get('subjects/assign-teacher', ['App\Http\Controllers\SubjectController', 'assignTeacherView'])->name('subjects.assign-teacher');
        Route::post('subjects/assign-teacher/{teacher}', ['App\Http\Controllers\SubjectController', 'assignTeacher'])->name('subjects.assign-teacher-to-subject');

        // subject routes
        Route::resource('subjects', SubjectController::class);

        // notice routes
        Route::resource('notices', NoticeController::class);
    });
});
