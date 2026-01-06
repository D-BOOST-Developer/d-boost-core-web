<?php

use App\Http\Controllers\Inventory\InventoryTransactionController;
use App\Http\Controllers\Inventory\InventoryDashboardController;
use App\Http\Controllers\Finance\FinanceDashboardController;
use App\Http\Controllers\Finance\SalesReturnController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // ============================================
    // INVENTORY MANAGEMENT ROUTES
    // ============================================
    
    Route::prefix('inventory')->name('inventory.')->group(function () {
        
        // Dashboard (Main Overview)
        Route::get('/dashboard', [InventoryDashboardController::class, 'index'])->name('dashboard');
        
        // Inventory Transactions (Simplified - Auto-approved)
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [InventoryTransactionController::class, 'index'])->name('index');
            Route::get('/create', [InventoryTransactionController::class, 'create'])->name('create');
            Route::post('/', [InventoryTransactionController::class, 'store'])->name('store');
            Route::get('/export', [InventoryTransactionController::class, 'export'])->name('export');
            Route::get('/product/{product}/history', [InventoryTransactionController::class, 'productHistory'])->name('product-history');
            Route::get('/{transaction}', [InventoryTransactionController::class, 'show'])->name('show');
        });
    });

    // ============================================
    // FINANCE MANAGEMENT ROUTES
    // ============================================
    
    Route::prefix('finance')->name('finance.')->group(function () {
        
        // Dashboard (Main Overview)
        Route::get('/dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard');
        
        // Real-time Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/profit-loss', [FinanceDashboardController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/cash-flow', [FinanceDashboardController::class, 'cashFlow'])->name('cash-flow');
        });
        
        // Accounts Receivable
        Route::get('/accounts-receivable', [FinanceDashboardController::class, 'accountsReceivable'])->name('accounts-receivable');
        
        // Sales Returns
        Route::prefix('sales-returns')->name('sales-returns.')->group(function () {
            Route::get('/', [SalesReturnController::class, 'index'])->name('index');
            Route::get('/create', [SalesReturnController::class, 'create'])->name('create');
            Route::post('/', [SalesReturnController::class, 'store'])->name('store');
            Route::get('/{salesReturn}', [SalesReturnController::class, 'show'])->name('show');
            Route::get('/{salesReturn}/edit', [SalesReturnController::class, 'edit'])->name('edit');
            Route::patch('/{salesReturn}', [SalesReturnController::class, 'update'])->name('update');
            Route::post('/{salesReturn}/complete', [SalesReturnController::class, 'complete'])->name('complete');
            Route::post('/{salesReturn}/cancel', [SalesReturnController::class, 'cancel'])->name('cancel');
            Route::get('/report', [SalesReturnController::class, 'report'])->name('report');
            Route::delete('/{salesReturn}', [SalesReturnController::class, 'destroy'])->name('destroy');
        });


    });
});
