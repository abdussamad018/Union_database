<?php

use App\Http\Controllers\Admin\CustomFieldDefinitionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\HouseController;
use App\Http\Controllers\Admin\ResidentController as AdminResidentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BariRep\DashboardController as BariDashboardController;
use App\Http\Controllers\BariRep\ResidentController as BariResidentController;
use App\Http\Controllers\DonationViewerController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResidentViewerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/api/public-stats', [LandingController::class, 'publicStats']);

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:social_organization,elite,super_admin'])->group(function () {
        Route::get('/donations', [DonationViewerController::class, 'index'])->name('donations.index');
        Route::get('/donations/{donation}', [DonationViewerController::class, 'show'])->name('donations.show');

        Route::get('/residents', [ResidentViewerController::class, 'index'])->name('residents.viewer.index');
        Route::get('/residents/{resident}', [ResidentViewerController::class, 'show'])->name('residents.viewer.show');
    });

    Route::prefix('bari')->middleware(['role:bari_representative'])->name('bari.')->group(function () {
        Route::get('/dashboard', [BariDashboardController::class, 'index'])->name('dashboard');
        Route::get('/residents', [BariResidentController::class, 'index'])->name('residents.index');
        Route::get('/residents/create', [BariResidentController::class, 'create'])->name('residents.create');
        Route::post('/residents', [BariResidentController::class, 'store'])->name('residents.store');
        Route::get('/residents/{resident}/edit', [BariResidentController::class, 'edit'])->name('residents.edit');
        Route::put('/residents/{resident}', [BariResidentController::class, 'update'])->name('residents.update');
        Route::delete('/residents/{resident}', [BariResidentController::class, 'destroy'])->name('residents.destroy');
        Route::post('/residents/{resident}/verify', [BariResidentController::class, 'verify'])->name('residents.verify');
    });

    Route::prefix('admin')->middleware(['role:super_admin'])->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/quick-decision', [AdminDashboardController::class, 'quickDecision'])->name('quick-decision');
        Route::post('/assign-aid', [AdminDashboardController::class, 'assignAid'])->name('assign-aid');
        Route::get('/activity-logs', [AdminDashboardController::class, 'activityLogs'])->name('activity-logs');

        Route::get('/residents', [AdminResidentController::class, 'index'])->name('residents.index');
        Route::get('/residents/create', [AdminResidentController::class, 'create'])->name('residents.create');
        Route::post('/residents', [AdminResidentController::class, 'store'])->name('residents.store');
        Route::get('/residents/{resident}/edit', [AdminResidentController::class, 'edit'])->name('residents.edit');
        Route::put('/residents/{resident}', [AdminResidentController::class, 'update'])->name('residents.update');
        Route::delete('/residents/{resident}', [AdminResidentController::class, 'destroy'])->name('residents.destroy');
        Route::get('/residents-export', [AdminResidentController::class, 'export'])->name('residents.export');

        Route::get('/houses', [HouseController::class, 'index'])->name('houses.index');
        Route::post('/houses', [HouseController::class, 'store'])->name('houses.store');
        Route::put('/houses/{house}', [HouseController::class, 'update'])->name('houses.update');

        Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
        Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');

        Route::get('/donations', [AdminDonationController::class, 'index'])->name('donations.index');
        Route::get('/donations/create', [AdminDonationController::class, 'create'])->name('donations.create');
        Route::post('/donations', [AdminDonationController::class, 'store'])->name('donations.store');
        Route::delete('/donations/{donation}', [AdminDonationController::class, 'destroy'])->name('donations.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        Route::get('/custom-fields', [CustomFieldDefinitionController::class, 'index'])->name('custom-fields.index');
        Route::post('/custom-fields', [CustomFieldDefinitionController::class, 'store'])->name('custom-fields.store');
        Route::put('/custom-fields/{custom_field_definition}', [CustomFieldDefinitionController::class, 'update'])->name('custom-fields.update');
        Route::post('/custom-fields/{custom_field_definition}/toggle', [CustomFieldDefinitionController::class, 'toggle'])->name('custom-fields.toggle');
        Route::post('/custom-fields/reorder', [CustomFieldDefinitionController::class, 'reorder'])->name('custom-fields.reorder');
    });
});

require __DIR__.'/auth.php';
