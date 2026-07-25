<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('/places', PlaceController::class);
Route::resource('/tasks', TaskController::class);
Route::resource('/volunteers', VolunteerController::class);
Route::resource('/assignments',AssignmentController::class);
Route::get('/run-migrations-secret-path-123', function () {
    try {
        // تحديث الـ autolander والتعرف على الحزم الجديدة
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        // تنفيذ المايجريشن
        Artisan::call('migrate', ['--force' => true]);

        // تثبيت مفاتيح Passport المفقودة على السيرفر
        Artisan::call('passport:install', ['--force' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Migrations and Passport installed successfully!',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});
