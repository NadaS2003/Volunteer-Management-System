<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VolunteerController;
use Laravel\Passport\Client;
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

Route::get('/create-passport-client-999', function () {
    try {
        // التحقق إن لم يكن العميل موجوداً مسبقاً لعدم تكراره
        if (Client::where('personal_access_client', 1)->exists()) {
            return "Personal Access Client already exists!";
        }

        // إنشاء العميل المطلوب لـ Passport برمجياً
        app(\Laravel\Passport\ClientRepository::class)->createPersonalAccessClient(
            null,
            'Personal Access Client',
            config('app.url')
        );

        return "Personal Access Client created successfully! You can now log in.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
