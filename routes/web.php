<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\StatusHistoryController;
use App\Http\Controllers\Admin\VisaApplicationController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\VisaStatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/visa-status', [VisaStatusController::class, 'show'])->name('visa-status');
Route::post('/visa-status/check', [VisaStatusController::class, 'check'])
    ->middleware('throttle:10,1')
    ->name('visa-status.check');
Route::get('/visa-status/captcha', [VisaStatusController::class, 'refreshCaptcha'])
    ->middleware('throttle:20,1')
    ->name('visa-status.captcha');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/applications', [VisaApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/create', [VisaApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [VisaApplicationController::class, 'store'])->name('applications.store');
        Route::get('/applications/{application}', [VisaApplicationController::class, 'show'])->name('applications.show');
        Route::get('/applications/{application}/edit', [VisaApplicationController::class, 'edit'])->name('applications.edit');
        Route::put('/applications/{application}', [VisaApplicationController::class, 'update'])->name('applications.update');
        Route::delete('/applications/{application}', [VisaApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::patch('/applications/{application}/status', [VisaApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::get('/applications/{application}/confirmation', [VisaApplicationController::class, 'confirmation'])->name('applications.confirmation');
        Route::get('/applications/{application}/pdf', [VisaApplicationController::class, 'pdf'])->name('applications.pdf');
        Route::get('/applications/{application}/photograph', [VisaApplicationController::class, 'photograph'])->name('applications.photograph');

        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::patch('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');

        Route::get('/status-histories', [StatusHistoryController::class, 'index'])->name('status-histories.index');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});