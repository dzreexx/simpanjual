<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryManagementController extends Controller
{
    public function stockmonitoring(Request $request)
    {
        $brandId = session('brand_id');
        $warehouseId = $request->input('warehouse_id');

        $warehouses = \App\Models\Warehouses::all();
        $products = collect(); // Default empty collection

        if ($brandId && $warehouseId) {
            $products = \App\Models\Products::where('id_brand', $brandId)->paginate(10);
            $products->appends($request->all());

            foreach ($products as $product) {
                // Actual Stock
                $actualStock = \App\Models\Inventory::where('id_product', $product->id_product)
                    ->where('id_warehouse', $warehouseId)
                    ->value('stock') ?? 0;

                // Reserved Stock
                $soReserve = \App\Models\SalesOrder::where('id_product', $product->id_product)
                    ->where('source_location', $warehouseId)
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->sum('total_amount');

                $trReserve = \App\Models\StockTransferItem::where('id_product', $product->id_product)
                    ->whereHas('transfer', function($q) use ($warehouseId) {
                        $q->where('source_location', $warehouseId);
                    })
                    ->where('item_status', '!=', 'accepted at warehouse')
                    ->sum('quantity');

                $reserveStock = $soReserve + $trReserve;

                // Pre-order Stock
                $poPreorder = \App\Models\PurchaseOrder::where('id_product', $product->id_product)
                    ->where('id_warehouse', $warehouseId)
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->sum('stock');

                $trPreorder = \App\Models\StockTransferItem::where('id_product', $product->id_product)
                    ->whereHas('transfer', function($q) use ($warehouseId) {
                        $q->where('destination_location', $warehouseId);
                    })
                    ->where('item_status', '!=', 'accepted at warehouse')
                    ->sum('quantity');

                $preOrderStock = $poPreorder + $trPreorder;

                $product->actual_stock = $actualStock;
                $product->reserve_stock = $reserveStock;
                $product->available_stock = $actualStock - $reserveStock;
                $product->preorder_stock = $preOrderStock;
            }
        }

        return view('pages.inventory.stockmonitoring', compact('warehouses', 'products', 'warehouseId', 'brandId'));
    }

    public function safetystock()
    {
        return view('pages.inventory.safetystock');
    }

    public function preorderstock()
    {
        return view('pages.inventory.preorderstock');
    }

    public function bundlestock()
    {
        return view('pages.inventory.bundlestock');
    }
}
