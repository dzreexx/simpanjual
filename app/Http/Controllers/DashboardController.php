<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;

class DashboardController extends Controller
{
    public function inbound(Request $request)
    {
        $brand_id = session('brand_id');
        $products = Products::where('id_brand', $brand_id)->get();
        $product_name = [];
        foreach ($products as $index => $product) {
            $product_name[$index] = $product->product_name;
        }

        // dd($product_name);
        // dd($products->product_name);
        return view('pages.dashboards.inbound', compact('brand_id', 'product_name'));
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
