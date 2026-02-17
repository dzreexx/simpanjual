<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\WithdrawalRequestController;
use App\Http\Controllers\InventoryManagementController;
use App\Http\Controllers\ItemManagementController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\BrandSessionController;
use App\Http\Controllers\Productcontroller;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/set-brand', [BrandSessionController::class, 'store'])->name('set-brand');

Route::get('addproduct', [ProductController::class, 'index']);
Route::post('addproduct', [ProductController::class, 'storeproduct'])->name('addproduct');

Route::get('addbrand', [BrandsController::class, 'addbrand']);
Route::post('addbrand', [BrandsController::class, 'storebrand'])->name('addbrand');

Route::get('addwarehouse', [WarehouseController::class, 'addwarehouse']);
Route::post('addwarehouse', [WarehouseController::class, 'storewarehouse'])->name('addwarehouse');

Route::get('adjuststock', [Productcontroller::class, 'adjuststock']);
Route::post('adjuststock', [Productcontroller::class, 'storestock'])->name('adjuststock');

Route::get('addpurchaseorder', [PurchaseOrderController::class, 'createPurchaseOrder'])->name('addpurchaseorder');
Route::post('addpurchaseorder', [PurchaseOrderController::class, 'storePurchaseOrder'])->name('storepurchaseorder');

// Route for User

Route::get('logcheck', [UserController::class, 'logcheck']);
Route::get('logout', [UserController::class, 'logout']);
// Route::get('dashboard', [UserController::class, 'dashboard']);

Route::get('login', [UserController::class, 'login'])->middleware('guest')->name('login');
Route::post('login', [UserController::class, 'authtentication']);

Route::get('daftar', [UserController::class, 'register']);
Route::post('daftar', [UserController::class, 'storeregister']);

// Dashboard route
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/inbound', [DashboardController::class, 'inbound'])
        ->name('dashboard.inbound');
    Route::post('/inbound', [DashboardController::class, 'inboundsetproduct'])
        ->name('dashboard.inboundsetproduct');
    Route::get('/outbound', [DashboardController::class, 'outbound'])->name('dashboard.outbound');
    Route::get('/stockledger', [DashboardController::class, 'stockledger'])->name('dashboard.stockledger');
});


// Sales Order
Route::get('salesorder', [SalesOrderController::class, 'index'])->middleware('auth')->name('salesorder');

// Purchase Order
Route::get('purchaseorder', [PurchaseOrderController::class, 'index'])->middleware('auth')->name('purchaseorder');

// Stock Transfer
Route::get('stocktransfer', [StockTransferController::class, 'index'])->middleware('auth')->name('stocktransfer');

// Withdrawal Request
Route::get('withdrawalrequest', [WithdrawalRequestController::class, 'index'])->middleware('auth')->name('withdrawalrequest');

// Inventory Management
Route::prefix('inventory')->middleware('auth')->group(function () {
    Route::get('/stockmonitoring', [InventoryManagementController::class, 'stockmonitoring'])->name('inventory.stockmonitoring');
    Route::get('/safetystock', [InventoryManagementController::class, 'safetystock'])->name('inventory.safetystock');
    Route::get('/preorderstock', [InventoryManagementController::class, 'preorderstock'])->name('inventory.preorderstock');
    Route::get('/bundlestock', [InventoryManagementController::class, 'bundlestock'])->name('inventory.bundlestock');
});

// Item Management
Route::prefix('item')->middleware('auth')->group(function () {
    Route::get('/itemmaster', [ItemManagementController::class, 'itemmaster'])->name('item.itemmaster');
    Route::get('/itempublished', [ItemManagementController::class, 'itempublished'])->name('item.itempublished');
    Route::get('/downloaditem', [ItemManagementController::class, 'downloaditem'])->name('item.downloaditem');
});

