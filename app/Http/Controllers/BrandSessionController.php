<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id_brand',
        ]);

        session(['brand_id' => $request->brand_id]);

        return redirect()->back();
    }
}
