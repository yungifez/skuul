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
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('schools', SchoolController::class);
});