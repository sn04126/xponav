<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExhibitController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\InteractiveSessionController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\QRCodeController;
use App\Http\Controllers\Api\HeatMapController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Social Authentication is handled via WEB routes (not API) — see routes/web.php
// OAuth requires session/cookie support for the redirect flow.
// Unity opens: {APP_URL}/auth/{provider} → OAuth → callback → deep link back to Unity

// Native SDK social auth — Unity sends the Google idToken / Facebook accessToken directly
// POST /api/auth/social-token  →  { provider: "google"|"facebook", token: "..." }
Route::post('/auth/social-token', [SocialAuthController::class, 'handleNativeToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);              // Get current user profile
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/password/update', [AuthController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Exhibit favorites and visited
    Route::get('/exhibits/favorites', [ExhibitController::class, 'favorites']);
    Route::get('/exhibits/visited', [ExhibitController::class, 'visited']);
    Route::post('/exhibits/{exhibit}/favorite', [ExhibitController::class, 'toggleFavorite']);
    Route::post('/exhibits/{exhibit}/visited', [ExhibitController::class, 'markAsVisited']);
    
    Route::apiResource('exhibits', ExhibitController::class);
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('feedback', FeedbackController::class);
    
    // Interactive Sessions
    Route::get('/interactive-sessions/favorites', [InteractiveSessionController::class, 'favorites']);
    Route::post('/interactive-sessions/{interactiveSession}/favorite', [InteractiveSessionController::class, 'toggleFavorite']);
    Route::post('/interactive-sessions/{interactiveSession}/book', [InteractiveSessionController::class, 'book']);
    Route::apiResource('interactive-sessions', InteractiveSessionController::class);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Payment Methods
    Route::post('/payment-methods/{paymentMethod}/set-default', [PaymentMethodController::class, 'setDefault']);
    Route::apiResource('payment-methods', PaymentMethodController::class)->except(['update']);

    // Subscriptions
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'getPlans']);
    Route::get('/subscriptions/status', [SubscriptionController::class, 'getStatus']);
    Route::post('/subscriptions/initiate', [SubscriptionController::class, 'initiate']);
    Route::post('/subscriptions/verify', [SubscriptionController::class, 'verify']);
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel']);

    // Stripe Payment (Checkout Session)
    Route::post('/payment/checkout', [PaymentController::class, 'checkout']);


    // Floor Plans (Admin CRUD)
    Route::apiResource('floor-plans', App\Http\Controllers\FloorPlanController::class);
    
    // AR Anchors (Admin CRUD)
    Route::apiResource('ar-anchors', App\Http\Controllers\ARAnchorController::class);
    Route::post('/ar-anchors/{id}/update-world-map', [App\Http\Controllers\ARAnchorController::class, 'updateWorldMap']);
});

// Public AR endpoints for mobile app (no auth required for AR functionality)
Route::prefix('ar')->group(function () {
    // Get floor plans for an exhibit
    Route::get('/exhibits/{exhibitId}/floor-plans', [App\Http\Controllers\FloorPlanController::class, 'byExhibit']);
    
    // Get AR data for a specific floor plan
    Route::get('/floor-plans/{id}/ar-data', [App\Http\Controllers\FloorPlanController::class, 'getARData']);
    
    // Get AR anchors for a floor plan
    Route::get('/floor-plans/{floorPlanId}/anchors', [App\Http\Controllers\ARAnchorController::class, 'byFloorPlan']);
    
    // Get reference points for AR initialization
    Route::get('/floor-plans/{floorPlanId}/reference-points', [App\Http\Controllers\ARAnchorController::class, 'getReferencePoints']);
    
    // Calculate user position based on detected anchors
    Route::post('/calculate-position', [App\Http\Controllers\ARAnchorController::class, 'calculatePosition']);

    // Get navigation path between two anchors
    Route::post('/navigation-path', [App\Http\Controllers\ARAnchorController::class, 'getNavigationPath']);

    // Log exhibit visit (can work with or without auth)
    Route::post('/log-visit/{exhibit}', [AnalyticsController::class, 'logVisit']);
});

// Public analytics endpoints
Route::prefix('analytics')->group(function () {
    // Get most visited exhibits (public)
    Route::get('/most-visited', [AnalyticsController::class, 'mostVisited']);

    // Position tracking for heat maps (from Unity app)
    Route::post('/position-tracks', [HeatMapController::class, 'storePositionTracks']);

    // Get heat map data (public for now)
    Route::get('/heat-map', [HeatMapController::class, 'getHeatMapData']);

    // Get exhibit heat map summary
    Route::get('/heat-map/exhibit/{exhibitId}', [HeatMapController::class, 'getExhibitHeatMapSummary']);
});

// Protected analytics endpoints (require auth)
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    // Admin dashboard analytics
    Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);

    // User's visit history
    Route::get('/my-history', [AnalyticsController::class, 'userHistory']);

    // Detailed exhibit analytics (admin)
    Route::get('/exhibits/{exhibit}', [AnalyticsController::class, 'exhibitAnalytics']);
});

// QR Code endpoints (public - for scanning)
Route::prefix('qr')->group(function () {
    // Scan QR code and get location data (main endpoint for Unity app)
    Route::get('/scan/{code}', [QRCodeController::class, 'scan']);
});

// Protected QR Code endpoints (admin management)
Route::middleware('auth:sanctum')->prefix('qr-codes')->group(function () {
    // Get all QR codes for an exhibit
    Route::get('/exhibits/{exhibit}', [QRCodeController::class, 'byExhibit']);

    // CRUD operations
    Route::post('/', [QRCodeController::class, 'store']);
    Route::put('/{qrCode}', [QRCodeController::class, 'update']);
    Route::delete('/{qrCode}', [QRCodeController::class, 'destroy']);

    // Regenerate QR code
    Route::post('/{qrCode}/regenerate', [QRCodeController::class, 'regenerateCode']);

    // Statistics
    Route::get('/statistics/{exhibit}', [QRCodeController::class, 'statistics']);
});

// Stripe Webhook (no auth — signature verified inside PaymentController)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Navigation Sessions (authenticated)
Route::middleware('auth:sanctum')->prefix('navigation-sessions')->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\NavigationSessionController::class, 'store']);
    Route::put('/{session}', [\App\Http\Controllers\Api\NavigationSessionController::class, 'update']);
    Route::post('/event', [\App\Http\Controllers\Api\NavigationSessionController::class, 'logEvent']);
    Route::get('/', [\App\Http\Controllers\Api\NavigationSessionController::class, 'index']);
    Route::get('/stats', [\App\Http\Controllers\Api\NavigationSessionController::class, 'stats']);
});

// Navigation Targets (public - for Unity app)
Route::get('/exhibits/{exhibitId}/navigation-targets', [\App\Http\Controllers\Api\NavigationTargetController::class, 'byExhibit']);

