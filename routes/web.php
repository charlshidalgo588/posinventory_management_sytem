<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PurchaseRecordController;

/*
|--------------------------------------------------------------------------
| VUE FRONTEND (SEPARATE SPA)
|--------------------------------------------------------------------------
| This tells the browser that Vue is running separately (localhost:5173).
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return response()->json(['message' => 'Vue frontend running separately']);
});


/*
|--------------------------------------------------------------------------
| 🌟 PUBLIC WEB ROUTES
|--------------------------------------------------------------------------
| Only for web session-based features.
| (API routes live in api.php, NOT here)
|--------------------------------------------------------------------------
*/

/* Auto-SKU generator must stay PUBLIC, Vue needs it */
Route::get('/api/generate-sku/{name}', [ProductController::class, 'generateSku'])
    ->name('products.generateSku');


/*
|--------------------------------------------------------------------------
| SPA AUTH ROUTES (WEB middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('spa.login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('spa.logout');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

});


/*
|--------------------------------------------------------------------------
| AUTH-REQUIRED BACKEND (Blade admin panel only)
|--------------------------------------------------------------------------
| Vue does NOT use these. These are only for Blade dashboard pages.
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN HOME PAGE (Blade)
    |--------------------------------------------------------------------------
    */
    Route::get('/home', [HomeController::class, 'index'])->name('home');


    /*
    |--------------------------------------------------------------------------
    | ⭐ BLADE DASHBOARD DATA (still used by your admin panel)
    |--------------------------------------------------------------------------
    */
    Route::get('/api/dashboard', [HomeController::class, 'dashboard'])
        ->name('dashboard.api');


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ROUTES (BLADE FORM PAGES)
    |--------------------------------------------------------------------------
    */
    Route::post('/products/{product}/restock', [ProductController::class, 'restock'])
        ->name('products.restock');

    // Admin product list
    Route::get('/product-list', [ProductController::class, 'index'])->name('products.index');

    // Blade product create form
    Route::get('/new-item', [ProductController::class, 'create'])->name('products.create');
    Route::post('/new-item', [ProductController::class, 'store'])->name('products.store');

    // Blade product edit & view
    Route::get('/product-overview/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');
    Route::put('/product-overview/{product}', [ProductController::class, 'update'])
        ->name('products.update');

    // Delete
    Route::delete('/product-overview/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');

    // Product Overview Page (Blade)
    Route::get('/product-overview/{product}', [ProductController::class, 'show'])
        ->name('products.show');

    Route::get('/product-transaction', [ProductController::class, 'inventoryStatus'])
        ->name('products.transaction');


    /*
    |--------------------------------------------------------------------------
    | CATEGORY ROUTES (Blade admin only)
    |--------------------------------------------------------------------------
    */
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');


    /*
    |--------------------------------------------------------------------------
    | SUPPLIER ROUTES (Blade admin only)
    |--------------------------------------------------------------------------
    */
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');


    /*
    |--------------------------------------------------------------------------
    | SALES (Blade POS)
    |--------------------------------------------------------------------------
    */
    Route::get('/point-of-sale', [POSController::class, 'index'])->name('pos.index');
    Route::get('/sales-transaction', [SaleController::class, 'index'])->name('sales.transaction');

    Route::get('/sales-receipt', fn() => view('sales-receipt'))->name('receipt');
    Route::post('/sales/process', [SaleController::class, 'processSale']);

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::delete('/sales/bulk-delete', [SaleController::class, 'bulkDelete'])
        ->name('sales.bulk-delete');


    /*
    |--------------------------------------------------------------------------
    | REPORTS (Blade admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY LOGS (Blade admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/inventory-logs', [InventoryLogController::class, 'index'])
        ->name('inventory.logs');


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION ROUTES (Blade admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/products/{product}/transactions', [TransactionController::class, 'productTransactions'])
        ->name('products.transactions');

    Route::get('/products/{product}/history', [TransactionController::class, 'productHistory'])
        ->name('products.history');

    Route::get('/transactions/history', [TransactionController::class, 'getTransactionHistory'])
        ->name('transactions.history');

    Route::get('/transactions/summary', [TransactionController::class, 'getTransactionSummary'])
        ->name('transactions.summary');


    /*
    |--------------------------------------------------------------------------
    | PURCHASE RECORDS
    |--------------------------------------------------------------------------
    */
    Route::post('/purchases', [PurchaseRecordController::class, 'store'])
        ->name('purchases.store');

    Route::get('/products/{product}/purchases', [PurchaseRecordController::class, 'productPurchases'])
        ->name('purchases.product');

    Route::get('/suppliers/{supplier}/purchases', [PurchaseRecordController::class, 'supplierPurchases'])
        ->name('purchases.supplier');
});
