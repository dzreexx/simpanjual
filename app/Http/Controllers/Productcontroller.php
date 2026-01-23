<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;
use App\Models\Products;

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
        $product->stock = $request->stock;
        // dd($product);
        $product->save();

        return redirect()->route('addproduct')->with('success', 'Product added successfully');
    }
}
