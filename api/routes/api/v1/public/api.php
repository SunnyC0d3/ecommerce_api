<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\EmailVerificationController;

// Other Controllers
use App\Http\Controllers\V1\Public\UserController;
use App\Http\Controllers\V1\Public\VendorController;
use App\Http\Controllers\V1\Public\ProductController;
use App\Http\Controllers\V1\Public\OrderController;
use App\Http\Controllers\V1\Public\PaymentController;
use App\Http\Controllers\V1\Public\ReturnsController;
use App\Http\Controllers\V1\Public\CartController;
use App\Http\Controllers\V1\Public\ReviewController;
use App\Http\Controllers\V1\Public\ReviewResponseController;

// Auth

// Route::middleware(['throttle:3,1', 'client'])
Route::controller(AuthController::class)
    ->group(function () {
        Route::post('/register', 'register')->middleware('rate_limit:auth.register')->name('auth.register');
        Route::post('/login', 'login')->middleware('rate_limit:auth.login')->name('auth.login');
    });

Route::middleware(['auth:api', 'account_lock', 'password_expiry'])
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('/logout', 'logout')->name('auth.logout');
        Route::post('/change-password', 'changePassword')->middleware('rate_limit:auth.password_reset')->name('password.change');
        Route::get('/security-info', 'getSecurityInfo')->name('auth.security-info');
    });

// Email Verification

Route::prefix('email')
    ->middleware(['signed'])
    ->controller(EmailVerificationController::class)
    ->group(function () {
        Route::get('/verify/{id}/{hash}', 'verify')->name('verification.verify');
    });

Route::prefix('email')
    ->middleware(['auth:api'])
    ->controller(EmailVerificationController::class)
    ->group(function () {
        Route::get('/resend', 'resend')->name('verification.resend');
    });

// Password Reset

Route::middleware(['throttle:3,5', 'client'])
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('/forgot-password', 'forgotPassword')->middleware('rate_limit:auth.password_reset')->name('password.email');
        Route::post('/reset-password', 'passwordReset')->middleware('rate_limit:auth.password_reset')->name('password.update');
    });

// Payments

Route::prefix('payments')
    ->middleware(['rate_limit:payments'])
    ->controller(PaymentController::class)
    ->group(function () {
        Route::post('/{gateway}/create', 'store')->name('payments.store');
        Route::post('/stripe/webhook', 'stripeWebhook')->name('payments.stripe.webhook');
        Route::post('/{gateway}/verify', 'verify')->name('payments.verify');
    });

// Users

Route::prefix('users')
    ->middleware(['auth:api', 'roles:user', 'emailVerified'])
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/{user}', 'show')->name('users.show');
        Route::post('/{user}', 'update')->name('users.update');
    });

// Vendors

Route::prefix('vendors')
    ->middleware(['auth:api', 'roles:vendor', 'emailVerified'])
    ->controller(VendorController::class)
    ->group(function () {
        Route::get('/{vendor}', 'show')->name('vendors.show');
        Route::post('/{vendor}', 'update')->name('vendors.update');
    });

// Products

Route::prefix('products')
    ->middleware(['client', 'rate_limit:search'])
    ->controller(ProductController::class)
    ->group(function () {
        Route::get('/', 'index')->name('products.index');
        Route::get('/{product}', 'show')->name('products.show');
    });

// Orders

Route::prefix('orders')
    ->middleware(['auth:api', 'roles:user, vendor', 'emailVerified'])
    ->controller(OrderController::class)
    ->group(function () {
        Route::get('/', 'index')->name('orders.index');
        Route::get('/{order}', 'show')->name('orders.show');
    });

// Returns

Route::prefix('returns')
    ->middleware(['auth:api', 'roles:user, vendor', 'emailVerified'])
    ->controller(ReturnsController::class)
    ->group(function () {
        Route::post('/', 'return')->name('returns');
    });

// Cart

Route::prefix('cart')
    ->middleware(['auth:api', 'rate_limit:cart'])
    ->controller(CartController::class)
    ->group(function () {
        Route::get('/', 'index')->name('cart.index');
        Route::post('/items', 'store')->name('cart.add');
        Route::post('/items/{cartItem}', 'update')->name('cart.update');
        Route::delete('/items/{cartItem}', 'destroy')->name('cart.remove');
        Route::delete('/clear', 'clear')->name('cart.clear');
        Route::post('/sync-prices', 'syncPrices')->name('cart.sync-prices');
    });

// Reviews
Route::prefix('products/{product}/reviews')
    ->middleware(['client', 'throttle:dynamic_rate_limit:guest.reviews'])
    ->controller(ReviewController::class)
    ->group(function () {
        Route::get('/', 'index')->name('products.reviews.index');
    });

Route::prefix('reviews')
    ->middleware(['client', 'throttle:dynamic_rate_limit:guest.reviews'])
    ->controller(ReviewController::class)
    ->group(function () {
        Route::get('/{review}', 'show')->name('reviews.show');
    });

// Reviews - Authenticated User Routes
Route::prefix('reviews')
    ->middleware(['auth:api', 'emailVerified'])
    ->controller(ReviewController::class)
    ->group(function () {
        Route::post('/', 'store')
            ->middleware('throttle:dynamic_rate_limit:reviews.create')
            ->name('reviews.store');

        Route::post('/{review}', 'update')
            ->middleware('throttle:dynamic_rate_limit:reviews.update')
            ->name('reviews.update');

        Route::delete('/{review}', 'destroy')
            ->middleware('throttle:dynamic_rate_limit:api.reviews')
            ->name('reviews.destroy');

        Route::post('/{review}/helpfulness', 'voteHelpfulness')
            ->middleware('throttle:dynamic_rate_limit:reviews.vote')
            ->name('reviews.helpfulness');

        Route::post('/{review}/report', 'report')
            ->middleware('throttle:dynamic_rate_limit:reviews.report')
            ->name('reviews.report');
    });

// Review Responses - Vendor Routes
Route::prefix('reviews/{review}/responses')
    ->middleware(['auth:api', 'roles:vendor', 'emailVerified'])
    ->controller(ReviewResponseController::class)
    ->group(function () {
        Route::post('/', 'store')
            ->middleware('throttle:dynamic_rate_limit:reviews.respond')
            ->name('review.responses.store');

        Route::post('/{response}', 'update')
            ->middleware('throttle:dynamic_rate_limit:vendor.responses')
            ->name('review.responses.update');

        Route::delete('/{response}', 'destroy')
            ->middleware('throttle:dynamic_rate_limit:vendor.responses')
            ->name('review.responses.destroy');
    });

// Vendor Response Management
Route::prefix('vendor/responses')
    ->middleware(['auth:api', 'roles:vendor', 'emailVerified', 'throttle:dynamic_rate_limit:vendor.dashboard'])
    ->controller(ReviewResponseController::class)
    ->group(function () {
        Route::get('/', 'index')->name('vendor.responses.index');
        Route::get('/{response}', 'show')->name('vendor.responses.show');
        Route::get('/unanswered/reviews', 'getUnansweredReviews')->name('vendor.responses.unanswered');
    });
