<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;

class BrandsController extends Controller
{
    public function addbrand()
    {
        return view('addbrand');
    }

    public function storebrand(Request $request)
    {
        $request->validate([
            'brand_name' => 'required',
        ]);

        $brand = new Brands();
        $brand->brand_name = $request->brand_name;
        $brand->save();

        return redirect()->route('addbrand')->with('success', 'Brand added successfully');
    }
}
