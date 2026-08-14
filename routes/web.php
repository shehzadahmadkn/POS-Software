<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User & Role Management
    Route::resource('users', UserController::class)->middleware('permission:manage-users');
    Route::resource('roles', RoleController::class)->middleware('permission:manage-roles');
    Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:view-activity-logs');
    Route::get('/statement', [App\Http\Controllers\AccountController::class, 'statement'])->name('statement.fetch');
    
    // General Settings
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'store'])->name('settings.store');

    
    // Product Catalog
    Route::resource('products', App\Http\Controllers\ProductController::class)->middleware('permission:manage-products|view-product-list');
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->middleware('permission:manage-product-categories');
    Route::resource('accounts', App\Http\Controllers\AccountController::class)->middleware('permission:manage-accounts|view-business-accounts');
    Route::resource('customers', App\Http\Controllers\CustomerController::class)->middleware('permission:manage-accounts|view-customer-accounts');
    Route::resource('vendors', App\Http\Controllers\VendorController::class)->middleware('permission:manage-accounts|view-vendor-accounts');
    Route::resource('group_accounts', App\Http\Controllers\GroupAccountController::class)->middleware('permission:manage-accounts|view-group-accounts');
    
    // Sales & Purchases
    Route::get('purchases/{purchase}/activity', [PurchaseController::class, 'activity'])->name('purchases.activity')->middleware('permission:view-purchase-history');
    Route::resource('purchases', PurchaseController::class)->middleware('permission:create-purchase|view-purchase-history|edit-purchase|delete-purchase');
    Route::get('sales/{sale}/activity', [SaleController::class, 'activity'])->name('sales.activity')->middleware('permission:view-sales-history');
    Route::resource('sales', SaleController::class)->middleware('permission:create-sale|view-sales-history|edit-sale|delete-sale');

    // Transactions
    Route::resource('transactions', App\Http\Controllers\TransactionController::class)->only(['index', 'store', 'destroy'])->middleware('permission:manage-deposit-withdrawal');
    Route::resource('payment_receives', App\Http\Controllers\PaymentReceiveController::class)->only(['index', 'store', 'destroy'])->middleware('permission:manage-payment-receiving');
    Route::resource('transfers', App\Http\Controllers\TransferController::class)->except(['create', 'show', 'edit'])->middleware('permission:manage-transfer');
    Route::resource('expense_categories', App\Http\Controllers\ExpenseCategoryController::class)->only(['index', 'store', 'update'])->middleware('permission:manage-expenses');
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class)->only(['index', 'store', 'destroy'])->middleware('permission:manage-expenses');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/profit-loss', [App\Http\Controllers\ReportController::class, 'profitLoss'])->name('profit_loss')->middleware('permission:view-profit-loss');
        Route::get('/daily-cash-book', [App\Http\Controllers\ReportController::class, 'dailyCashBook'])->name('daily_cash_book')->middleware('permission:view-daily-cash-book');
        Route::get('/product-wise-sales', [App\Http\Controllers\ReportController::class, 'productWiseSales'])->name('product_wise_sales')->middleware('permission:view-product-wise-sales');
        Route::get('/ledger-report', [App\Http\Controllers\ReportController::class, 'ledgerReport'])->name('ledger_report')->middleware('permission:view-ledger-report');
        Route::get('/stock-report', [App\Http\Controllers\ReportController::class, 'stockReport'])->name('stock_report')->middleware('permission:view-stock-report');
        Route::get('/customer-wise-sales', [App\Http\Controllers\ReportController::class, 'customerWiseSales'])->name('customer_wise_sales')->middleware('permission:view-customer-wise-sales');
    });

    // Todo List
    Route::post('/todos/{id}/toggle', [App\Http\Controllers\TodoController::class, 'toggle'])->name('todos.toggle');
    Route::resource('todos', App\Http\Controllers\TodoController::class)->except(['create', 'show', 'edit']);

    // Stocks
    Route::get('/stocks', [App\Http\Controllers\StockController::class, 'index'])->name('stocks.index')->middleware('permission:view-stock|view-stock-zero|view-stock-above-zero|view-stock-below-zero');
    Route::get('/stocks/details', [App\Http\Controllers\StockController::class, 'details'])->name('stocks.details')->middleware('permission:view-stock');
    Route::get('/stocks/adjustment', [App\Http\Controllers\StockController::class, 'adjustmentList'])->name('stocks.adjustment')->middleware('permission:manage-stock-adjustment');
    Route::post('/stocks/adjustment', [App\Http\Controllers\StockController::class, 'adjustStore'])->name('stocks.adjustment.store')->middleware('permission:manage-stock-adjustment');
    Route::delete('/stocks/adjustment/{id}', [App\Http\Controllers\StockController::class, 'adjustDestroy'])->name('stocks.adjustment.destroy')->middleware('permission:manage-stock-adjustment');

    // Warehouses & Stock Transfers
    Route::resource('warehouses', App\Http\Controllers\WarehouseController::class)->except(['create', 'show', 'edit'])->middleware('permission:manage-warehouses');
    Route::resource('stock-transfers', App\Http\Controllers\StockTransferController::class)->only(['index', 'create', 'store'])->middleware('permission:manage-stock-transfers');

    // Quotations
    Route::resource('quotations', App\Http\Controllers\QuotationController::class)->except(['edit', 'update']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
