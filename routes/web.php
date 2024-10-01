<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

// Home Route
Route::get('/', [ScheduleController::class, 'index'])->name('home');

// Route for the about page
Route::get('about', function () {
    return view('about');
})->name('about');

// Route for the search page
Route::get('/search', function () {
    return view('search');
})->name('search');

// Route for searching bookings
Route::get('/search-booking', [BookingController::class, 'search'])->name('search.booking');

// Route for fetching destinations based on selected origin (routeUpID)
Route::get('/fetch-destinations/{routeUpID}', [BookingController::class, 'fetchDestinations'])->name('booking.fetchDestinations');

// Store booking
Route::post('/booking/store', [BookingController::class, 'storeBooking'])->name('booking.store');

// Route for showing the booking form
Route::get('/booking', [BookingController::class, 'showBookingForm'])->name('booking.form');

// Route for showing booking details based on scheduleId
Route::get('/booking/{scheduleId}', [BookingController::class, 'showBooking'])->name('booking.show');

// Route for showing the customer form
Route::get('/customer', [BookingController::class, 'showCustomerForm'])->name('customer.form');

// Summary routes
Route::post('/booking/summary', [BookingController::class, 'showSummary'])->name('booking.summary');

// Back to customer
Route::get('/customer', [BookingController::class, 'showCustomerForm'])->name('customer');

// Upload Slip
Route::post('/booking/upload-slip', [BookingController::class, 'uploadSlip'])->name('booking.uploadSlip');

// Dashboard Admin route with ScheduleController
Route::get('/dashboard', [ScheduleController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authentication routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Fallback route to handle undefined routes
Route::fallback(function () {
    return "<h1>ไม่พบหน้าเว็บ</h1>"; // Properly closed HTML tag
});

// Include authentication routes
require __DIR__.'/auth.php';

// Booking confirmation route
Route::get('/booking/confirmation/{id}', [BookingController::class, 'showConfirmation'])->name('booking.confirmation');

// Route for showing the payment confirmation page
Route::get('/admin/confirm/{status}', [PaymentController::class, 'showConfirmation'])->name('admin.confirmation');

// Route for showing the payment detail
Route::get('/admin/payment/{paymentId}', [PaymentController::class, 'showPaymentDetail'])->name('admin.payment.detail');

//new route for showing the payment detail
Route::patch('/admin/payment/{paymentId}/update-status', [PaymentController::class, 'updatePaymentStatus'])->name('admin.payment.update-status');