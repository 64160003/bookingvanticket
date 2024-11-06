<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ReportController;


// Home Route
Route::get('/', [ScheduleController::class, 'index'])->name('home');

//Admin Booking
Route::get('/adminbooking', [ScheduleController::class, 'addindex'])
    ->name('addminbooking');

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
require __DIR__ . '/auth.php';

// Booking confirmation route
Route::get('/booking/confirmation/{id}', [BookingController::class, 'showConfirmation'])->name('booking.confirmation');



// API routes for AJAX calls
Route::get('/api/origins', [RouteController::class, 'getOrigins']);
Route::get('/api/destinations', [RouteController::class, 'getDestinations']);
Route::get('/api/origins/{id}', [RouteController::class, 'getOrigin']);
Route::get('/api/origins/{id}/destinations', [RouteController::class, 'getOriginDestinations']);
Route::post('/api/origins', [RouteController::class, 'storeOrigin']);
Route::post('/api/destinations', [RouteController::class, 'storeDestination']);
Route::put('/api/origins/{id}', [RouteController::class, 'updateOrigin']);
Route::put('/api/destinations/{id}', [RouteController::class, 'updateDestination']);
Route::delete('/api/origins/{id}', [RouteController::class, 'deleteOrigin']);
Route::delete('/api/destinations/{id}', [RouteController::class, 'deleteDestination']);

Route::prefix('admin')->group(function () {
    Route::get('/schedules/{id}', [ScheduleController::class, 'show'])->name('admin.schedules.show');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('admin.schedules.store');
    Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('admin.schedules.destroy');

    // Route สำหรับ toggle-active ต้องเป็น POST และต้องใช้ ScheduleID แทน {schedule}
    Route::post('/schedules/{ScheduleID}/toggle-active', [ScheduleController::class, 'toggleActive']);

    // Route for the search page Admin
    Route::get('/admin-search', function () {
        return view('admin.search');
    })->name('adsearch');
    // Route for searching bookings admin
    Route::get('/search-booking-admin', [BookingController::class, 'adsearch'])->name('adminsearch');

    // Route for showing the manage page
    Route::get('/admin/manage', [ManageController::class, 'show'])->name('admin.manage');

    //new route for showing the payment detail
    Route::patch('/admin/payment/{paymentId}/update-status', [PaymentController::class, 'updatePaymentStatus'])->name('admin.payment.update-status');

    // Route for showing the payment confirmation page
    Route::get('/admin/confirm/{status}', [PaymentController::class, 'showConfirmation'])->name('admin.confirmation');

    // Route for showing the payment detail
    Route::get('/admin/payment/{paymentId}', [PaymentController::class, 'showPaymentDetail'])->name('admin.payment.detail');



    //Admin update status
    Route::patch('/admin/payment/{paymentId}/update-status', [PaymentController::class, 'updatePaymentStatus'])
        ->name('admin.updatePaymentStatus');

    // New routes for managing routes
    Route::get('/admin/manage/route', [RouteController::class, 'index'])->name('admin.manageRoute');
    Route::get('/admin/manage-schedule', [ScheduleController::class, 'manageSchedule'])->name('admin.manageSchedule');
});

//route สำหรับรายงานยอดขาย
Route::prefix('admin')->group(function () {
    Route::get('report/total-sales', [ReportController::class, 'totalSales'])->name('report.totalSales');
    Route::get('report/daily-sales', [ReportController::class, 'dailySales'])->name('report.dailySales');
    Route::get('report/monthly-sales-comparison', [ReportController::class, 'monthlySalesComparison'])->name('report.monthlySalesComparison');
});