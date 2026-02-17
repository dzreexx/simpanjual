<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;
use App\Models\Products;
use App\Models\Warehouses;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;


class PurchaseOrderController extends Controller
{
    public function createPurchaseOrder()
    {
        $brands = Brands::all();
        $products = Products::all();
        $warehouses = Warehouses::all();
        return view('addpurchaseorder', compact('brands', 'products', 'warehouses'));
    }
    public function storePurchaseOrder(Request $request)
    {
        $request->validate([
            'id_warehouse' => 'required',
            'id_brand' => 'required',
            'id_product' => 'required',
            'stock' => 'required',
        ]);
        // dd($request->all());
        $purchase_order = new PurchaseOrder();
        $purchase_order->id_warehouse = $request->id_warehouse;
        $purchase_order->id_brand = $request->id_brand;
        $purchase_order->id_product = $request->id_product;
        $purchase_order->stock = $request->stock;
        $purchase_order->save();
    }
}
