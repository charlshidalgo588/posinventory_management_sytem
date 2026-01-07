<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES (NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

// 🟦 Dashboard JSON for Vue Home Page
Route::get('/dashboard', [HomeController::class, 'dashboard']);

// 🟦 Vue Product List (POS / Inventory)
Route::get('/product-list', [ProductController::class, 'productList']);

// 🟦 Category Public JSON
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// 🟦 Supplier Public JSON
Route::get('/suppliers', [SupplierController::class, 'apiIndex']);
Route::get('/suppliers/{id}', [SupplierController::class, 'apiShow']);

// 🟦 Product Public JSON
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/stock', [ProductController::class, 'getStock']);

// 🟦 Auto SKU Generator
Route::get('/generate-sku/{name}', [ProductController::class, 'generateSku']);

// 🟦 POS Sale Processing
Route::post('/sales/process', [SaleController::class, 'processSale']);

// 🟦 Sales List (Read-only)
Route::get('/sales', [SaleController::class, 'apiList']);

// 🟦 View Single Sale
Route::get('/sales/{id}', [SaleController::class, 'apiShow']);

// 🟦 Delete Sales (Admin validation inside controller)
Route::post('/sales/bulk-delete', [SaleController::class, 'bulkDelete']);

// 🟦 Reports JSON
Route::get('/reports', [ReportsController::class, 'index']);

// 🟦 Inventory Logs JSON
Route::get('/inventory/logs', [InventoryLogController::class, 'index']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER (SANCTUM)
|--------------------------------------------------------------------------
*/

// 🔐 Get authenticated user (used by Layout, Settings, Profile restore)
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (REQUIRE LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // 🧪 API Health Check
    Route::get('/test', fn () => response()->json('Laravel API Connected Successfully!'));

    /*
    |--------------------------------------------------------------------------
    | USER SETTINGS / PROFILE
    |--------------------------------------------------------------------------
    */

    // ✅ Update name & email
    Route::put('/user', [AuthController::class, 'update']);

    // ✅ Update password
    Route::put('/user/password', [AuthController::class, 'updatePassword']);

    /*
    |--------------------------------------------------------------------------
    | CATEGORY CRUD
    |--------------------------------------------------------------------------
    */
    Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);

    Route::post('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | PRODUCT CRUD
    |--------------------------------------------------------------------------
    */
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);

    // Vue PUT fallback
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::post('/products/{id}', [ProductController::class, 'update']);

    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // 🔄 Product Restock
    Route::post('/products/{id}/restock', [ProductController::class, 'restock']);

    /*
    |--------------------------------------------------------------------------
    | PRODUCT TRANSACTIONS (VUE)
    |--------------------------------------------------------------------------
    */

    // ✅ Product Transactions Table
    Route::get('/products/{id}/transactions', [TransactionController::class, 'apiProductTransactions']);

    // ✅ Product Summary Cards
    Route::get('/products/{id}/summary', [TransactionController::class, 'apiProductSummary']);

    // ✅ Product History Tab
    Route::get('/products/{id}/history', [TransactionController::class, 'apiProductHistory']);

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER CRUD
    |--------------------------------------------------------------------------
    */
    Route::post('/suppliers', [SupplierController::class, 'apiStore']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'apiUpdate']);
    Route::patch('/suppliers/{id}', [SupplierController::class, 'apiUpdate']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'apiDestroy']);
});
