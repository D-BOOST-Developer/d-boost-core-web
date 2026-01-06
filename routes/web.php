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
    });
    
    // Customer Relations (CRM) - Comprehensive
    Route::prefix('customers')->name('customers.')->group(function () {
        // Main customer CRUD
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');  
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        
        // Analytics & Reporting (must be before /{customer})
        Route::get('/analytics/dashboard', [\App\Http\Controllers\CRM\CustomerAnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
        Route::get('/analytics/rfm', [\App\Http\Controllers\CRM\CustomerAnalyticsController::class, 'rfmAnalysis'])->name('analytics.rfm');
        Route::get('/analytics/cohort', [\App\Http\Controllers\CRM\CustomerAnalyticsController::class, 'cohortAnalysis'])->name('analytics.cohort');
        Route::get('/analytics/churn', [\App\Http\Controllers\CRM\CustomerAnalyticsController::class, 'churnAnalysis'])->name('analytics.churn');
        
        // Export/Import (must be before /{customer})
        Route::get('/export/excel', [CustomerController::class, 'export'])->name('export');
        Route::post('/import', [CustomerController::class, 'import'])->name('import');
        
        // Interactions (must be before /{customer})
        Route::resource('interactions', \App\Http\Controllers\CRM\CustomerInteractionController::class);
        Route::get('/interactions/calendar/view', [\App\Http\Controllers\CRM\CustomerInteractionController::class, 'calendar'])->name('interactions.calendar');
        
        // Segments (must be before /{customer})
        Route::resource('segments', \App\Http\Controllers\CRM\CustomerSegmentController::class);
        Route::post('/segments/{segment}/refresh', [\App\Http\Controllers\CRM\CustomerSegmentController::class, 'refresh'])->name('segments.refresh');
        Route::get('/segments/{segment}/export', [\App\Http\Controllers\CRM\CustomerSegmentController::class, 'export'])->name('segments.export');
        
        // Loyalty Program (must be before /{customer})
        Route::get('/loyalty', [\App\Http\Controllers\CRM\CustomerLoyaltyController::class, 'index'])->name('loyalty.index');
        Route::get('/loyalty/leaderboard', [\App\Http\Controllers\CRM\CustomerLoyaltyController::class, 'leaderboard'])->name('loyalty.leaderboard');
        
        // Feedback (must be before /{customer})
        Route::resource('feedback', \App\Http\Controllers\CRM\CustomerFeedbackController::class);
        Route::post('/feedback/{feedback}/respond', [\App\Http\Controllers\CRM\CustomerFeedbackController::class, 'respond'])->name('feedback.respond');
        Route::patch('/feedback/{feedback}/publish', [\App\Http\Controllers\CRM\CustomerFeedbackController::class, 'togglePublish'])->name('feedback.publish');
        
        // Customer detail routes (after all resource routes)
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/{customer}/loyalty', [\App\Http\Controllers\CRM\CustomerLoyaltyController::class, 'customerPoints'])->name('loyalty.points');
        Route::post('/{customer}/loyalty/adjust', [\App\Http\Controllers\CRM\CustomerLoyaltyController::class, 'adjustPoints'])->name('loyalty.adjust');
        Route::post('/{customer}/interactions', [\App\Http\Controllers\CRM\CustomerInteractionController::class, 'storeForCustomer'])->name('interactions.storeForCustomer');
        Route::post('/{customer}/notes', [CustomerController::class, 'addNote'])->name('notes.store');
        Route::post('/{customer}/wishlist/{product}', [CustomerController::class, 'addToWishlist'])->name('wishlist.add');
        Route::delete('/{customer}/wishlist/{wishlist}', [CustomerController::class, 'removeFromWishlist'])->name('wishlist.remove');
    });
    
    // Sales & Expenses (under Finance)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::resource('sales', SaleController::class);
        Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
        
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
    
    // DEBY AI - Business Assistant
    Route::prefix('api/deby-ai')->name('deby-ai.')->group(function () {
        Route::post('/chat', [DebyAIController::class, 'chat'])->name('chat');
        Route::post('/chatgpt', [DebyAIController::class, 'chatgpt'])->name('chatgpt');
        Route::post('/thinking', [DebyAIController::class, 'callThinkingAPI'])->name('thinking');
        Route::get('/history', [DebyAIController::class, 'history'])->name('history');
        Route::get('/quick-tips', [DebyAIController::class, 'quickTips'])->name('quick-tips');
        Route::get('/onboarding-tutorial', [DebyAIController::class, 'onboardingTutorial'])->name('onboarding-tutorial');
        Route::get('/search-faq', [DebyAIController::class, 'searchFAQ'])->name('search-faq');
        Route::get('/faq-by-category', [DebyAIController::class, 'faqByCategory'])->name('faq-by-category');
        Route::post('/confirm-action', [DebyAIController::class, 'confirmAction'])->name('confirm-action');
        Route::post('/reset', [DebyAIController::class, 'resetConversation'])->name('reset');
        
        // Chat Log Management
        Route::post('/save-log', [DebyAIController::class, 'saveChatLog'])->name('save-log');
        Route::get('/chat-history', [DebyAIController::class, 'getChatHistory'])->name('chat-history');
        Route::delete('/chat-history', [DebyAIController::class, 'deleteChatHistory'])->name('delete-history');
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
    Route::post('{slug}/track/{link}', [\App\Http\Controllers\Storefront\PublicStorefrontController::class, 'trackClick'])
        ->name('track-click');
});

// Inventory & Finance Routes
require base_path('routes/inventory-finance.php');

require __DIR__.'/auth.php';
