<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (no authentication required)
Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin']);
Route::post('/register', [RegisteredUserController::class, 'apiRegister']);

// Protected API routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });
    
    // Profile API Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'apiShow']);
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'apiUpdate']);
    Route::put('/profile/business', [App\Http\Controllers\ProfileController::class, 'apiUpdateBusiness']);
    Route::post('/profile/business/logo', [App\Http\Controllers\ProfileController::class, 'apiUploadLogo']);
    Route::post('/profile/business/qris', [App\Http\Controllers\ProfileController::class, 'apiUploadQris']);
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'apiUpdatePassword']);
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'apiDestroy']);

    // Dashboard API Routes
    Route::get('/dashboard/summary', [App\Http\Controllers\Api\DashboardController::class, 'summary']);
    Route::get('/dashboard/transactions', [App\Http\Controllers\Api\DashboardController::class, 'transactions']);
    Route::get('/dashboard/analytics', [App\Http\Controllers\Api\DashboardController::class, 'analytics']);

    // Sales API Routes
    Route::get('/sales', [App\Http\Controllers\Api\SalesController::class, 'index']);
    Route::get('/sales/invoice-number', [App\Http\Controllers\Api\SalesController::class, 'invoiceNumber']);
    Route::get('/sales/products', [App\Http\Controllers\Api\SalesController::class, 'products']);

    Route::post('/sales', [App\Http\Controllers\Api\SalesController::class, 'store']);
    Route::get('/sales/{id}', [App\Http\Controllers\Api\SalesController::class, 'show']);
    Route::put('/sales/{id}', [App\Http\Controllers\Api\SalesController::class, 'update']);
    Route::delete('/sales/{id}', [App\Http\Controllers\Api\SalesController::class, 'destroy']);

    // Expenses API Routes
    Route::get('/expenses', [App\Http\Controllers\Api\ExpenseController::class, 'index']);
    Route::get('/expenses/categories', [App\Http\Controllers\Api\ExpenseController::class, 'categories']);
    Route::get('/expenses/stats', [App\Http\Controllers\Api\ExpenseController::class, 'stats']);
    Route::post('/expenses', [App\Http\Controllers\Api\ExpenseController::class, 'store']);
    Route::get('/expenses/{id}', [App\Http\Controllers\Api\ExpenseController::class, 'show']);
    Route::put('/expenses/{id}', [App\Http\Controllers\Api\ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [App\Http\Controllers\Api\ExpenseController::class, 'destroy']);

    // Products API Routes
    Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/products/dashboard', [App\Http\Controllers\Api\ProductController::class, 'dashboard']);
    Route::get('/products/categories', [App\Http\Controllers\Api\ProductController::class, 'categories']);
    Route::post('/products', [App\Http\Controllers\Api\ProductController::class, 'store']);
    Route::get('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'show']);
    Route::put('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'update']);
    Route::delete('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy']);
    Route::post('/products/{id}/toggle-status', [App\Http\Controllers\Api\ProductController::class, 'toggleStatus']);
    Route::post('/products/{id}/adjust-stock', [App\Http\Controllers\Api\ProductController::class, 'adjustStock']);

    // Storefront API Routes
    Route::get('/storefront', [App\Http\Controllers\Api\StorefrontController::class, 'index']);
    Route::get('/storefront/config', [App\Http\Controllers\Api\StorefrontController::class, 'getConfig']);
    Route::put('/storefront/config', [App\Http\Controllers\Api\StorefrontController::class, 'updateConfig']);
    Route::post('/storefront/config/logo', [App\Http\Controllers\Api\StorefrontController::class, 'uploadLogo']);
    Route::post('/storefront/config/publish', [App\Http\Controllers\Api\StorefrontController::class, 'publish']);

    // Marketplace Links API Routes
    Route::get('/storefront/links', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'index']);
    Route::get('/storefront/links/{id}', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'show']);
    Route::post('/storefront/links', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'store']);
    Route::put('/storefront/links/{id}', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'update']);
    Route::delete('/storefront/links/{id}', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'destroy']);
    Route::patch('/storefront/links/{id}/toggle', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'toggleStatus']);
    Route::post('/storefront/links/reorder', [App\Http\Controllers\Api\MarketplaceLinkController::class, 'reorder']);

    // Deby AI v2 API Routes (Mobile App) — single AI endpoint + DB-only quick actions
    Route::post('/mobile/deby-ai/chat', [App\Http\Controllers\DebyAIController::class, 'chat']);
    Route::get('/mobile/deby-ai/chat-history', [App\Http\Controllers\DebyAIController::class, 'getChatHistory']);
    Route::delete('/mobile/deby-ai/chat-history', [App\Http\Controllers\DebyAIController::class, 'deleteChatHistory']);

    Route::get('/mobile/deby-ai/quick/revenue',     [App\Http\Controllers\DebyAIController::class, 'quickRevenue']);
    Route::get('/mobile/deby-ai/quick/expenses',    [App\Http\Controllers\DebyAIController::class, 'quickExpenses']);
    Route::get('/mobile/deby-ai/quick/stock',       [App\Http\Controllers\DebyAIController::class, 'quickStock']);
    Route::get('/mobile/deby-ai/quick/sales-today', [App\Http\Controllers\DebyAIController::class, 'quickSalesToday']);
    Route::get('/mobile/deby-ai/quick/products',    [App\Http\Controllers\DebyAIController::class, 'quickProducts']);
    Route::get('/mobile/deby-ai/quick/low-stock',   [App\Http\Controllers\DebyAIController::class, 'quickLowStock']);
    // Reports API Routes
    Route::get('/reports/profit-loss', [App\Http\Controllers\Api\ReportController::class, 'profitLoss']);
    Route::get('/reports/profit-loss/download', [App\Http\Controllers\Api\ReportController::class, 'downloadProfitLoss']);
    Route::get('/reports/cash-flow', [App\Http\Controllers\Api\ReportController::class, 'cashFlow']);
    Route::get('/reports/cash-flow/download', [App\Http\Controllers\Api\ReportController::class, 'downloadCashFlow']);
});
