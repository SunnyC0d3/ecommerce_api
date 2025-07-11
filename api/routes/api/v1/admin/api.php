<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers

use App\Http\Controllers\V1\Admin\UserController;
use App\Http\Controllers\V1\Admin\VendorController;
use App\Http\Controllers\V1\Admin\PermissionController;
use App\Http\Controllers\V1\Admin\RoleController;
use App\Http\Controllers\V1\Admin\RolePermissionController;
use App\Http\Controllers\V1\Admin\ProductController;
use App\Http\Controllers\V1\Admin\ProductAttributeController;
use App\Http\Controllers\V1\Admin\ProductCategoryController;
use App\Http\Controllers\V1\Admin\ProductTagController;
use App\Http\Controllers\V1\Admin\PaymentMethodController;
use App\Http\Controllers\V1\Admin\OrderController;
use App\Http\Controllers\V1\Admin\ReturnsController;
use App\Http\Controllers\V1\Admin\RefundController;
use App\Http\Controllers\V1\Admin\PaymentController;
use App\Http\Controllers\V1\Admin\InventoryController;
use App\Http\Controllers\V1\Admin\ReviewController;
use App\Http\Controllers\V1\Admin\ReviewResponseController;

// Admin/Users

Route::prefix('admin/users')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.users.index');
        Route::get('/{user}', 'show')->name('admin.users.show');
        Route::post('/', 'store')->name('admin.users.store');
        Route::post('/{user}', 'update')->name('admin.users.update');
        Route::delete('/{user}', 'destroy')->name('admin.users.destroy');
    });

// Admin/Vendors

Route::prefix('admin/vendors')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(VendorController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.vendors.index');
        Route::get('/{vendor}', 'show')->name('admin.vendors.show');
        Route::post('/', 'store')->name('admin.vendors.store');
        Route::post('/{vendor}', 'update')->name('admin.vendors.update');
        Route::delete('/{vendor}', 'destroy')->name('admin.vendors.destroy');
    });

// Admin/Permissions

Route::prefix('admin/permissions')
    ->middleware(['auth:api', 'roles:super admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(PermissionController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.permissions.index');
        Route::post('/', 'store')->name('admin.permissions.store');
        Route::post('/{permission}', 'update')->name('admin.permissions.update');
        Route::delete('/{permission}', 'destroy')->name('admin.permissions.destroy');
    });

// Admin/Roles

Route::prefix('admin/roles')
    ->middleware(['auth:api', 'roles:super admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(RoleController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.roles.index');
        Route::post('/', 'store')->name('admin.roles.store');
        Route::post('/{role}', 'update')->name('admin.roles.update');
        Route::delete('/{role}', 'destroy')->name('admin.roles.destroy');
    });

// Admin/RolePermission

Route::prefix('admin')
    ->middleware(['auth:api', 'roles:super admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(RolePermissionController::class)
    ->group(function () {
        Route::get('roles/{role}/permissions', 'index')->name('admin.rolepermission.index');
        Route::post('roles/{role}/permissions', 'assign')->name('admin.rolepermission.assign');
        Route::post('roles/{role}/permissions/assign-all', 'assignAllPermissions')->name('admin.rolepermission.assignAll');
        Route::delete('roles/{role}/permissions/{permission}', 'revoke')->name('admin.rolepermission.revoke');
    });

// Admin/Products

Route::prefix('admin/products')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ProductController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.products.index');
        Route::get('/{product}', 'show')->name('admin.products.show');
        Route::post('/', 'store')->name('admin.products.store');
        Route::post('/{product}', 'update')->name('admin.products.update');
        Route::delete('/soft-destroy/{product}', 'softDestroy')->name('admin.products.softDestroy');
        Route::delete('/{id}', 'destroy')->name('admin.products.destroy');
        Route::patch('/{id}/restore', 'restore')->name('admin.products.restore');
    });

// Admin/Product Attributes

Route::prefix('admin/product-attributes')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ProductAttributeController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.products.attributes.index');
        Route::post('/', 'store')->name('admin.products.attributes.store');
        Route::get('/{productAttribute}', 'show')->name('admin.products.attributes.show');
        Route::post('/{productAttribute}', 'update')->name('admin.products.attributes.update');
        Route::delete('/{productAttribute}', 'destroy')->name('admin.products.attributes.destroy');
    });

// Admin/Product Categories

Route::prefix('admin/product-categories')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ProductCategoryController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.products.categories.index');
        Route::post('/', 'store')->name('admin.products.categories.store');
        Route::get('/{productCategory}', 'show')->name('admin.products.categories.show');
        Route::post('/{productCategory}', 'update')->name('admin.products.categories.update');
        Route::delete('/{productCategory}', 'destroy')->name('admin.products.categories.destroy');
    });

// Admin/Product Tags

Route::prefix('admin/product-tags')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ProductTagController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.products.tags.index');
        Route::post('/', 'store')->name('admin.products.tags.store');
        Route::get('/{productTag}', 'show')->name('admin.products.tags.show');
        Route::post('/{productTag}', 'update')->name('admin.products.tags.update');
        Route::delete('/{productTag}', 'destroy')->name('admin.products.tags.destroy');
    });

// Admin/Payment Methods

Route::prefix('admin/payment-methods')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(PaymentMethodController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.paymentmethods.index');
        Route::post('/', 'store')->name('admin.paymentmethods.store');
        Route::post('/{paymentMethod}', 'update')->name('admin.paymentmethods.update');
        Route::delete('/{paymentMethod}', 'destroy')->name('admin.paymentmethods.destroy');
    });

// Admin/Orders

Route::prefix('admin/orders')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(OrderController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.orders.index');
        Route::post('/', 'store')->name('admin.orders.store');
        Route::get('/{order}', 'show')->name('admin.orders.show');
        Route::post('/{order}', 'update')->name('admin.orders.update');
        Route::delete('/{order}', 'destroy')->name('admin.orders.destroy');
        Route::delete('/{id}/force-delete', 'forceDelete')->name('admin.orders.forceDelete');
        Route::patch('/{id}/restore', 'restore')->name('admin.orders.restore');
    });

// Admin/Returns

Route::prefix('admin/returns')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ReturnsController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.returns.index');
        Route::post('/{returnId}/{action}', 'reviewReturn')->name('admin.returns.action');
    });

// Admin/Refund

Route::prefix('admin/refunds')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(RefundController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.refunds.index');
        Route::post('/{gateway}/{id}', 'refund')->name('admin.refund');
    });

// Admin/Refund

Route::prefix('admin/payments')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(PaymentController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.payments.index');
    });

// Admin/Inventory

Route::prefix('admin/inventory')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(InventoryController::class)
    ->group(function () {
        Route::get('/overview', 'overview')->name('admin.inventory.overview');
        Route::post('/products/{product}/threshold', 'updateProductThreshold')->name('admin.inventory.product.threshold');
        Route::post('/variants/{variant}/threshold', 'updateVariantThreshold')->name('admin.inventory.variant.threshold');
        Route::post('/check', 'manualCheck')->name('admin.inventory.check');
        Route::post('/bulk-update-thresholds', 'bulkUpdateThresholds')->name('admin.inventory.bulk.update');
    });

// Admin/Reviews - Complete Review Management System

Route::prefix('admin/reviews')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ReviewController::class)
    ->group(function () {
        Route::get('/', 'index')->name('admin.reviews.index');
        Route::get('/{review}', 'show')->name('admin.reviews.show');
        Route::delete('/{review}', 'destroy')->name('admin.reviews.destroy');

        Route::post('/{review}/moderate', 'moderate')->name('admin.reviews.moderate');
        Route::post('/bulk-moderate', 'bulkModerate')->name('admin.reviews.bulk-moderate');

        Route::get('/reports', 'getReportedReviews')->name('admin.reviews.reports');
        Route::post('/reports/{report}/handle', 'handleReport')->name('admin.reviews.reports.handle');

        Route::get('/analytics', 'getAnalytics')->name('admin.reviews.analytics');
    });

// Admin/Review Responses - Manage vendor responses

Route::prefix('admin/review-responses')
    ->middleware(['auth:api', 'roles:super admin,admin', 'emailVerified', 'rate_limit:admin'])
    ->controller(ReviewResponseController::class)
    ->group(function () {
        Route::get('/', 'adminIndex')->name('admin.review-responses.index');
        Route::get('/{response}', 'adminShow')->name('admin.review-responses.show');
        Route::post('/{response}/approve', 'approve')->name('admin.review-responses.approve');
        Route::delete('/{response}', 'adminDestroy')->name('admin.review-responses.destroy');
        Route::post('/bulk-moderate', 'bulkModerate')->name('admin.review-responses.bulk-moderate');
        Route::get('/analytics', 'getAnalytics')->name('admin.review-responses.analytics');
    });
