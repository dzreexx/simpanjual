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

Route::get('addsalesorder', [SalesOrderController::class, 'createSalesOrder'])->name('addsalesorder');
Route::get('getwarehouses/{id_product}', [SalesOrderController::class, 'getWarehouses']);
Route::post('addsalesorder', [SalesOrderController::class, 'storeSalesOrder'])->name('storesalesorder');

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
Route::post('salesorder/import', [SalesOrderController::class, 'importSO'])->middleware('auth')->name('salesorder.import');
Route::get('salesorder/template', [SalesOrderController::class, 'downloadTemplate'])->middleware('auth')->name('salesorder.template');
Route::get('salesorder/export', [SalesOrderController::class, 'exportExcel'])->middleware('auth')->name('salesorder.export');
Route::get('salesorder/detail/{so_number}', [SalesOrderController::class, 'show'])->middleware('auth')->name('salesorder.detail');
Route::patch('salesorder/{so_number}/status', [SalesOrderController::class, 'updateStatus'])->middleware('auth')->name('salesorder.updateStatus');
Route::get('salesorder/{status?}', [SalesOrderController::class, 'index'])->middleware('auth')->name('salesorder');

// Purchase Order
Route::post('purchaseorder/import', [PurchaseOrderController::class, 'importPO'])->middleware('auth')->name('purchaseorder.import');
Route::get('purchaseorder/template', [PurchaseOrderController::class, 'downloadTemplate'])->middleware('auth')->name('purchaseorder.template');
Route::get('purchaseorder/export', [PurchaseOrderController::class, 'exportExcel'])->middleware('auth')->name('purchaseorder.export');
Route::get('purchaseorder/detail/{id}', [PurchaseOrderController::class, 'show'])->middleware('auth')->name('purchaseorder.detail');
Route::patch('purchaseorder/{id}/status', [PurchaseOrderController::class, 'updateStatus'])->middleware('auth')->name('purchaseorder.updateStatus');
Route::get('purchaseorder/{status?}', [PurchaseOrderController::class, 'index'])->middleware('auth')->name('purchaseorder');

// Stock Transfer
Route::middleware('auth')->group(function () {
    Route::post('stocktransfer', [StockTransferController::class, 'store'])->name('stocktransfer.store');
    Route::get('stocktransfer/check-stock', [StockTransferController::class, 'checkStock'])->name('stocktransfer.checkStock');
    Route::get('stocktransfer/detail/{tr_number}', [StockTransferController::class, 'show'])->name('stocktransfer.show');
    Route::patch('stocktransfer/item/{id}/status', [StockTransferController::class, 'updateItemStatus'])->name('stocktransfer.updateItemStatus');
    Route::patch('stocktransfer/{tr_number}/status', [StockTransferController::class, 'updateStatus'])->name('stocktransfer.updateStatus');
    Route::delete('stocktransfer/{tr_number}', [StockTransferController::class, 'destroy'])->name('stocktransfer.destroy');
    Route::get('stocktransfer/{status?}', [StockTransferController::class, 'index'])->name('stocktransfer');
});

// Withdrawal Request
Route::middleware('auth')->group(function () {
    Route::post('withdrawalrequest', [WithdrawalRequestController::class, 'store'])->name('withdrawalrequest.store');
    Route::get('withdrawalrequest/detail/{wr_number}', [WithdrawalRequestController::class, 'show'])->name('withdrawalrequest.show');
    Route::patch('withdrawalrequest/{wr_number}/status', [WithdrawalRequestController::class, 'updateStatus'])->name('withdrawalrequest.updateStatus');
    Route::delete('withdrawalrequest/{wr_number}', [WithdrawalRequestController::class, 'destroy'])->name('withdrawalrequest.destroy');
    Route::get('withdrawalrequest/{status?}', [WithdrawalRequestController::class, 'index'])->name('withdrawalrequest');
});

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

