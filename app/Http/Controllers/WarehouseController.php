<?php

namespace App\Http\Controllers;

use App\Models\Warehouses;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function addWarehouse()
    {
        return view('addWarehouse');
    }

    public function storewarehouse(Request $request)
    {
        $warehouse = new Warehouses();
        $warehouse->warehouse_name = $request->warehouse_name;
        $warehouse->save();
        return redirect()->route('addwarehouse');
    }
}
