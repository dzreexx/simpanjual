<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouses;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Inventory;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    // public function inbound(Request $request)
    // {
    //     $product_value = $request->input('product_id');
    //     // $selectedProduct = Products::where('id_product', $product_value)->get();
    //     $selectedProduct = '';


    //     $brand_id = session('brand_id');
    //     $products = Products::where('id_brand', $brand_id)->get();
    //     $product_name = [];
    //     foreach ($products as $index => $product) {
    //         $product_name[$index] = $product->product_name;
    //     }

    //     // dd($product_name);
    //     // dd($products->product_name);
    //     return view('pages.dashboards.inbound', compact('brand_id', 'product_name', 'products', 'product_value', 'selectedProduct'));
    //     // return view('pages.dashboards.inbound', compact('brand_id', 'products'));
    // }

    public function inbound(Request $request)
    {
        // dd($request->all());
        $product_value = $request->input('product_id');
        $warehouse_value = $request->input('warehouse_id');

        $brand_id = session('brand_id');
        $products = Products::where('id_brand', $brand_id)->get();
        $warehouses = Warehouses::get();

        $inventory = Inventory::where('id_product', $product_value)->where('id_warehouse', $warehouse_value)->get();
        $purchaseOrder = PurchaseOrder::where('id_product', $product_value)->where('id_warehouse', $warehouse_value)->get();
        $totalPendingStock = PurchaseOrder::where('status', 'pending')->where('id_product', $product_value)->where('id_warehouse', $warehouse_value)->sum('stock');
        $totalProcessStock = PurchaseOrder::where('status', 'process')->where('id_product', $product_value)->where('id_warehouse', $warehouse_value)->sum('stock');
        $totalDoneStock = PurchaseOrder::where('status', 'done')->where('id_product', $product_value)->where('id_warehouse', $warehouse_value)->sum('stock');


        $selectedProduct = null;
        if ($product_value) {
            $selectedProduct = Products::where('id_product', $product_value)->first();
        }
        $selectedWarehouse = null;
        if ($warehouse_value) {
            $selectedWarehouse = Warehouses::where('id_warehouse', $warehouse_value)->first();
        }

        return view(
            'pages.dashboards.inbound',
            compact('products', 'product_value', 'selectedProduct', 'warehouses', 'warehouse_value', 'selectedWarehouse', 'inventory', 'purchaseOrder', 'totalPendingStock', 'totalProcessStock', 'totalDoneStock')
        );
    }


    public function inboundsetproduct(Request $request)
    {
        return redirect()->route('dashboard.inbound', [
            'product_id' => $request->product_id,
            'warehouse_id' => $request->warehouse_id
        ]);
    }

    public function outbound()
    {

        return view('pages.dashboards.outbound');
    }

    public function stockledger()
    {
        return view('pages.dashboards.stockledger');
    }


}
