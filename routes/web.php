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
    return view('welcome');
})->name('home');

Route::group(['middleware' => ['auth:sanctum', 'verified'], 'prefix' => 'dashboard', 'namespace' => 'App\Http\Controllers'], function () {

 
    //School routes
    Route::resource('schools', SchoolController::class);
    Route::post('schools/set school', ['App\Http\Controllers\SchoolController', 'setSchool'])->name('schools.setSchool');

    Route::middleware(['App\Http\Middleware\EnsureSuperAdminHasSchoolId'])->group(function () {
        //dashboard route
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard');
        //manage school settings
        Route::get('Schools/settings', ['App\Http\Controllers\SchoolController', 'settings'])->name('schools.settings');
        //class routes
        Route::resource('classes', MyClassController::class);

        //class groups routes
        Route::resource('class-groups', ClassGroupController::class);

        //sections routes
        Route::resource('sections', SectionController::class);

        //student routes
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/print', ['App\Http\Controllers\StudentController', 'printProfile'])->name('students.print-profile');

        //teacher routes
        Route::resource('teachers', TeacherController::class);

        //academic year routes
        Route::resource('academic-years', AcademicYearController::class);
        Route::post('academic-years/set academic year', ['App\Http\Controllers\AcademicYearController', 'setAcademicYear'])->name('academic-years.set-academic-year');

        //subject routes
        Route::resource('subjects', SubjectController::class);
    });
});