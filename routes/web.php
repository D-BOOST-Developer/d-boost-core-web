<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModularDashboardController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Finance\SaleController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DebyAIController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Settings\ModuleController;
use App\Http\Controllers\Settings\MenuController;
use App\Http\Controllers\Settings\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Google OAuth Routes
Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Onboarding Routes (Auth required, but no onboarding check)
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'verified', 'onboarding.completed'])->group(function () {
    // Modular Dashboard (Main Landing Page)
    Route::get('/dashboard', [ModularDashboardController::class, 'index'])->name('dashboard');
    Route::get('/modules/{module}/details', [ModularDashboardController::class, 'getModuleDetails'])->name('modules.details');
    
    // Analytics Dashboard (Detailed Analytics)
    Route::get('/analytics', [DashboardController::class, 'index'])->name('analytics');
    Route::get('/analytics/sales-chart', [DashboardController::class, 'salesChart'])->name('analytics.sales-chart');
    Route::get('/analytics/profit-loss-chart', [DashboardController::class, 'profitLossChart'])->name('analytics.profit-loss-chart');
    Route::get('/analytics/hourly-pattern', [DashboardController::class, 'hourlyPattern'])->name('analytics.hourly-pattern');
    
    // Module Redirect - Auto navigate to first menu
    Route::get('/module/{moduleName}', [\App\Http\Controllers\ModuleRedirectController::class, 'redirectToModule'])->name('module.redirect');
    
    // Products (under Inventory)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('products/{product}/toggle-storefront', [ProductController::class, 'togglePublishedToStorefront'])->name('products.toggle-storefront');
    });
    

    
    // Sales & Expenses (under Finance)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::resource('sales', SaleController::class);
        Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
        Route::post('sales/{sale}/confirm', [SaleController::class, 'confirm'])->name('sales.confirm');
        Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        
        Route::resource('expenses', ExpenseController::class);
    });

    // Digital Storefront Management
    Route::prefix('storefront')->name('storefront.')->group(function () {
        // Dashboard/Overview
        Route::get('/', [\App\Http\Controllers\Storefront\StorefrontBuilderController::class, 'index'])
            ->name('index');
        
        // Marketplace Links Management
        Route::resource('links', \App\Http\Controllers\Storefront\MarketplaceLinkController::class);
        Route::patch('links/{link}/toggle', [\App\Http\Controllers\Storefront\MarketplaceLinkController::class, 'toggleStatus'])
            ->name('links.toggle');
        Route::post('links/reorder', [\App\Http\Controllers\Storefront\MarketplaceLinkController::class, 'reorder'])
            ->name('links.reorder');
        
        // Builder & Configuration
        Route::get('builder', [\App\Http\Controllers\Storefront\StorefrontBuilderController::class, 'edit'])
            ->name('builder.edit');
        Route::post('builder', [\App\Http\Controllers\Storefront\StorefrontBuilderController::class, 'update'])
            ->name('builder.update');
        Route::post('builder/logo', [\App\Http\Controllers\Storefront\StorefrontBuilderController::class, 'uploadLogo'])
            ->name('builder.upload-logo');
        Route::post('builder/publish', [\App\Http\Controllers\Storefront\StorefrontBuilderController::class, 'publish'])
            ->name('builder.publish');
        Route::get('preview', [\App\Http\Controllers\Storefront\StorefrontBuilderController::class, 'preview'])
            ->name('preview');
        
        // Client Reviews Management
        Route::get('reviews', [\App\Http\Controllers\Storefront\ClientReviewController::class, 'index'])
            ->name('reviews.index');
        Route::post('reviews/{review}/approve', [\App\Http\Controllers\Storefront\ClientReviewController::class, 'approve'])
            ->name('reviews.approve');
        Route::post('reviews/{review}/reject', [\App\Http\Controllers\Storefront\ClientReviewController::class, 'reject'])
            ->name('reviews.reject');
        Route::delete('reviews/{review}', [\App\Http\Controllers\Storefront\ClientReviewController::class, 'destroy'])
            ->name('reviews.destroy');
    });
    
    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
    
    // Marketing
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/campaigns', function () {
            return view('marketing.campaigns.index');
        })->name('campaigns.index');
    });
    
    
    // HR
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/employees', function () {
            return view('hr.employees.index');
        })->name('employees.index');
    });
    
    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');
    
    // Advanced Analytics (different from main analytics)
    Route::get('/advanced-analytics', function () {
        return view('analytics.advanced.index');
    })->name('analytics.index');
    
    // Audit Logs (Admin only)
    Route::middleware('can:admin')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });
    
    // Settings (Super Admin and Admin only)
    Route::prefix('settings')->name('settings.')->group(function () {
        // Modules Management
        Route::resource('modules', ModuleController::class);
        Route::patch('modules/{module}/toggle-status', [ModuleController::class, 'toggleStatus'])->name('modules.toggle-status');
        
        // Menus Management
        Route::resource('menus', MenuController::class);
        Route::patch('menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menus.toggle-status');
        
        // Roles Management
        Route::resource('roles', RoleController::class);
        Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::patch('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        
        // Users Management
        Route::resource('users', \App\Http\Controllers\Settings\UserController::class);
    });
    
    // DEBY AI v2 - Single AI endpoint (auto-context) + DB-only quick actions
    // Note: Mobile app uses routes in api.php with Sanctum auth
    Route::prefix('api/deby-ai')->name('deby-ai.')->group(function () {
        Route::post('/chat', [DebyAIController::class, 'chat'])->name('chat');
        Route::get('/chat-history', [DebyAIController::class, 'getChatHistory'])->name('chat-history');
        Route::delete('/chat-history', [DebyAIController::class, 'deleteChatHistory'])->name('delete-history');

        Route::prefix('quick')->name('quick.')->group(function () {
            Route::get('/revenue',     [DebyAIController::class, 'quickRevenue'])->name('revenue');
            Route::get('/expenses',    [DebyAIController::class, 'quickExpenses'])->name('expenses');
            Route::get('/stock',       [DebyAIController::class, 'quickStock'])->name('stock');
            Route::get('/sales-today', [DebyAIController::class, 'quickSalesToday'])->name('sales-today');
            Route::get('/products',    [DebyAIController::class, 'quickProducts'])->name('products');
            Route::get('/low-stock',   [DebyAIController::class, 'quickLowStock'])->name('low-stock');
        });
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/business', [ProfileController::class, 'updateBusinessProfile'])->name('profile.business.update');
    Route::post('/profile/upload-logo', [ProfileController::class, 'uploadLogo'])->name('profile.upload.logo');
    Route::post('/profile/upload-qris', [ProfileController::class, 'uploadQris'])->name('profile.upload.qris');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public Storefront Routes (No Auth Required)
Route::prefix('store')->name('public.store.')->group(function () {
    Route::get('{slug}', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'show'])
        ->name('show');
    Route::get('{slug}/leave-review', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'showReviewForm'])
        ->name('review-form');
    Route::post('{slug}/track/{link}', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'trackClick'])
        ->name('track-click');
    Route::post('{slug}/review', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'submitReview'])
        ->name('submit-review');

    // Catalog & E-commerce
    Route::get('{slug}/catalog', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'catalog'])
        ->name('catalog');
    Route::get('{slug}/product/{product}', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'productDetail'])
        ->name('product-detail');

    Route::get('{slug}/cart', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'viewCart'])
        ->name('cart');
    Route::post('{slug}/cart/add', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'addToCart'])
        ->name('cart.add');
    Route::patch('{slug}/cart/{productId}', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'updateCart'])
        ->name('cart.update');
    Route::delete('{slug}/cart/{productId}', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'removeFromCart'])
        ->name('cart.remove');

    Route::get('{slug}/checkout', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'checkoutForm'])
        ->name('checkout');
    Route::post('{slug}/checkout', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'placeOrder'])
        ->name('checkout.place');

    Route::get('{slug}/order/success/{invoice}', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'orderSuccess'])
        ->name('order-success');
        
    Route::get('{slug}/orders', [\App\Http\Controllers\Storefront\StorefrontOrderController::class, 'myOrders'])
        ->name('my-orders');
});

// Inventory & Finance Routes
require base_path('routes/inventory-finance.php');

require __DIR__.'/auth.php';
