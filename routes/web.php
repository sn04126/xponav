<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ExhibitController;
use App\Http\Controllers\FloorPlanController;
use App\Http\Controllers\ARAnchorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InteractiveSessionController;
use App\Http\Controllers\Admin\QRCodeController;
use App\Http\Controllers\Admin\HeatMapController;

Route::get('/', function () {
    return redirect('/admin/login');
});

// Social Authentication Routes (web routes for OAuth redirects)
// Unity opens system browser → these routes handle OAuth flow → redirect back to Unity via deep link
Route::get('/auth/{provider}', [\App\Http\Controllers\SocialAuthController::class, 'redirectToProvider'])
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\SocialAuthController::class, 'handleProviderCallback'])
    ->name('social.callback');

// Admin Login Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminDashboardController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminDashboardController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminDashboardController::class, 'logout'])->name('logout');
});

// Admin Routes (Protected - requires auth + admin role)
Route::prefix('admin')->name('admin.')->middleware(['auth:web', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

    // Exhibits Management
    Route::resource('exhibits', ExhibitController::class);

    // Floor Plans Management
    Route::resource('floor-plans', FloorPlanController::class);

    // AR Anchors Management
    Route::resource('ar-anchors', ARAnchorController::class);

    // Users Management
    Route::resource('users', UserController::class);

    // Interactive Sessions Management
    Route::resource('sessions', InteractiveSessionController::class);

    // QR Codes Management
    Route::resource('qr-codes', QRCodeController::class);
    Route::post('qr-codes/{qrCode}/regenerate', [QRCodeController::class, 'regenerate'])->name('qr-codes.regenerate');
    Route::get('qr-codes/{qrCode}/print', [QRCodeController::class, 'print'])->name('qr-codes.print');
    Route::get('exhibits/{exhibit}/qr-codes/print-all', [QRCodeController::class, 'printAll'])->name('qr-codes.print-all');

    // AJAX endpoints for QR code form
    Route::get('ajax/exhibits/{exhibit}/floor-plans', [QRCodeController::class, 'getFloorPlans'])->name('ajax.floor-plans');
    Route::get('ajax/floor-plans/{floorPlan}/anchors', [QRCodeController::class, 'getAnchors'])->name('ajax.anchors');

    // Plans Management
    Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class);

    // Subscription Management
    Route::get('subscriptions', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'index'])->name('subscriptions.index');

    // Navigation Analytics
    Route::get('navigation', [\App\Http\Controllers\Admin\NavigationAnalyticsController::class, 'index'])->name('navigation.index');

    // Heat Maps Analytics
    Route::get('heat-maps', [HeatMapController::class, 'index'])->name('heat-maps.index');
    Route::get('heat-maps/data', [HeatMapController::class, 'getData'])->name('heat-maps.data');
    Route::post('heat-maps/aggregate', [HeatMapController::class, 'aggregate'])->name('heat-maps.aggregate');
    Route::get('heat-maps/export', [HeatMapController::class, 'export'])->name('heat-maps.export');
});
