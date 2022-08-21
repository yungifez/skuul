<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/home', function () {
    return redirect()->route('dashboard');
});

//user must be authenticated
Route::middleware('auth:sanctum', 'verified', 'App\Http\Middleware\EnsureDefaultPasswordIsChanged')->prefix('dashboard')->namespace('App\Http\Controllers')->group(function () {

    //manage school settings
    Route::get('schools/settings', ['App\Http\Controllers\SchoolController', 'settings'])->name('schools.settings')->middleware('App\Http\Middleware\EnsureSuperAdminHasSchoolId');

    //School routes
    Route::resource('schools', SchoolController::class);
    Route::post('schools/set-school', ['App\Http\Controllers\SchoolController', 'setSchool'])->name('schools.setSchool');

    //super admin must have school id set
    Route::middleware(['App\Http\Middleware\EnsureSuperAdminHasSchoolId'])->group(function () {

        //dashboard route
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard');

        //class routes
        Route::resource('classes', MyClassController::class);

        //class groups routes
        Route::resource('class-groups', ClassGroupController::class);

        //sections routes
        Route::resource('sections', SectionController::class);

        Route::middleware(['App\Http\Middleware\EnsureAcademicYearIsSet'])->group(function () {

            //promotion routes
            Route::get('students/promotions', ['App\Http\Controllers\PromotionController', 'index'])->name('students.promotions');
            Route::get('students/promote', ['App\Http\Controllers\PromotionController', 'promoteView'])->name('students.promote');
            Route::post('students/promote', ['App\Http\Controllers\PromotionController', 'promote']);
            Route::get('students/promotions/{promotion}', ['App\Http\Controllers\PromotionController', 'show'])->name('students.promotions.show');
            Route::delete('students/promotions/{promotion}/reset', ['App\Http\Controllers\PromotionController', 'resetPromotion'])->name('students.promotions.reset');

            //graduation routes
            Route::get('students/graduations', ['App\Http\Controllers\GraduationController', 'index'])->name('students.graduations');
            Route::get('students/graduate', ['App\Http\Controllers\GraduationController', 'graduateView'])->name('students.graduate');
            Route::post('students/graduate', ['App\Http\Controllers\GraduationController', 'graduate']);
            Route::delete('students/graduations/{student}/reset', ['App\Http\Controllers\GraduationController', 'resetGraduation'])->name('students.graduations.reset');

            //semester routes
            Route::resource('semesters', SemesterController::class);
            Route::post('semesters/set', ['App\Http\Controllers\SemesterController', 'setSemester'])->name('semesters.set-semester');
        });

        Route::middleware(['App\Http\Middleware\EnsureSemesterIsSet'])->group(function () {
            //syllabi route
            Route::resource('syllabi', SyllabusController::class);

            //timetable route
            Route::resource('timetables', TimetableController::class);

            //manage timetable
            Route::get('timetables/{timetable}/manage', ['App\Http\Controllers\TimetableController', 'manage'])->name('timetables.manage');
            Route::get('timetables/{timetable}/print', ['App\Http\Controllers\TimetableController', 'print'])->name('timetables.print');

            //timetable-timeslot route
            Route::resource('timetables/{timetable}/manage/time-slots', TimetableTimeSlotController::class);
            Route::post('timetables/{timetable}/manage/time-slots/{time_slot}/record/create', ['App\Http\Controllers\TimetableTimeSlotController', 'addTimetableRecord'])->name('timetables.records.create')->scopeBindings();

            //set exam status
            Route::post('exams/{exam}/set-status', ['App\Http\Controllers\ExamController', 'setExamStatus'])->name('exams.set-status');

            // set publish result status
            Route::post('exams/{exam}/set-publish-result-status', ['App\Http\Controllers\ExamController', 'setPublishResultStatus'])->name('exams.set-publish-result-status');
            //manage exam record
            Route::resource('exams/exam-records', ExamRecordController::class);

            //exam tabulation sheet
            Route::get('exams/tabulation-sheet', ['App\Http\Controllers\ExamController', 'examTabulation'])->name('exams.tabulation');

            //result tabulation sheet
            Route::get('exams/result-tabulation-sheet', ['App\Http\Controllers\ExamController', 'resultTabulation'])->name('exams.result-tabulation');

            //result checker
            Route::get('exams/result-checker', ['App\Http\Controllers\ExamController', 'resultChecker'])->name('exams.result-checker');

            //exam routes
            Route::resource('exams', ExamController::class);

            //exam slot routes
            Route::scopeBindings()->group(function () {
                Route::resource('exams/{exam}/manage/exam-slots', ExamSlotController::class);
            });

            //grade system routes
            Route::resource('grade-systems', GradeSystemController::class);
        });

        //student routes
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/print', ['App\Http\Controllers\StudentController', 'printProfile'])->name('students.print-profile');

        //admin routes
        Route::resource('admins', AdminController::class);

        //teacher routes
        Route::resource('teachers', TeacherController::class);

        //parent routes
        Route::resource('parents', ParentController::class);
        Route::get('parents/{parent}/assign-students-to-parent', ['App\Http\Controllers\ParentController', 'assignStudentsView'])->name('parents.assign-students');
        Route::post('parents/{parent}/assign-students-to-parent', ['App\Http\Controllers\ParentController', 'assignStudent']);

        //academic year routes
        Route::resource('academic-years', AcademicYearController::class);
        Route::post('academic-years/set', ['App\Http\Controllers\AcademicYearController', 'setAcademicYear'])->name('academic-years.set-academic-year');

        //subject routes
        Route::resource('subjects', SubjectController::class);

        //notice routes
        Route::resource('notices', NoticeController::class);
    });
});
