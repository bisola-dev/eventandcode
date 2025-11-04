<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('events', App\Http\Controllers\EventController::class);
    Route::resource('guests', App\Http\Controllers\GuestController::class);
    Route::get('bulk', [App\Http\Controllers\GuestController::class, 'bulk'])->name('guests.bulk');
    Route::post('bulk', [App\Http\Controllers\GuestController::class, 'bulkStore'])->name('guests.bulk.store');
    Route::get('bulk/download-sample', [App\Http\Controllers\GuestController::class, 'downloadSample'])->name('guests.download-sample')->middleware('auth');
    Route::get('guest/validate/{qrCode}', [App\Http\Controllers\GuestController::class, 'validateQr'])->name('guest.validate');
    Route::post('guest/send-qr-email/{guest}', [App\Http\Controllers\GuestController::class, 'sendQrEmail'])->name('guest.send-qr-email');
    Route::post('guest/share-whatsapp/{guest}', [App\Http\Controllers\GuestController::class, 'shareWhatsApp'])->name('guest.share-whatsapp');
    Route::post('guest/send-all-whatsapp', [App\Http\Controllers\GuestController::class, 'sendAllWhatsApp'])->name('guest.send-all-whatsapp');
    Route::get('test-email/{guest}', [App\Http\Controllers\GuestController::class, 'testEmail'])->name('guest.test-email');
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('superadmin');
});

// Public routes for QR code display (no authentication required)
Route::get('guest/display/{qrCode}', [App\Http\Controllers\GuestController::class, 'displayGuest'])->name('guest.display');

require __DIR__.'/auth.php';
