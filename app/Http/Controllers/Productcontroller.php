<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Brands;
use App\Models\Products;
use App\Models\Warehouses;

class Productcontroller extends Controller
{
    public function index()
    {
        $brands = Brands::all();
        return view('addproduct', compact('brands'));
    }

    public function storeproduct(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'id_brand' => 'required',
            'price' => 'required',
            // 'stock' => 'required',
        ], [
            'product_name.required' => 'Nama produk tidak boleh kosong',
            'id_brand.required' => 'Brand tidak boleh kosong',
            'price.required' => 'Harga produk tidak boleh kosong',
            // 'stock.required' => 'produk tidak boleh kosong',
        ]);

        $product = new Products();
        $product->product_name = $request->product_name;
        $product->id_brand = $request->id_brand;
        $product->price = $request->price;
        // dd($product);
        $product->save();

        return redirect()->route('addproduct')->with('success', 'Product added successfully');
    }

    public function adjuststock()
    {
        $brand_id = session('brand_id');
        $products = Products::where('id_brand', $brand_id)->get();
        $warehouses = Warehouses::all();
        return view('adjuststock', compact('products', 'warehouses'));
    }
    public function storestock(Request $request)
    {
        $request->validate([
            'id_product' => 'required',
            'id_warehouse' => 'required',
        ], [
            'id_product.required' => 'Produk tidak boleh kosong',
            'id_warehouse.required' => 'Warehouse tidak boleh kosong',
        ]);

        $inventory = new Inventory();
        $inventory->id_product = $request->id_product;
        $inventory->id_warehouse = $request->id_warehouse;
        $inventory->stock = $request->stock;
        $inventory->save();

        return redirect()->route('adjuststock')->with('success', 'Product added successfully');
    }
}
