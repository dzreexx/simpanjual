<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryManagementController extends Controller
{
    public function stockmonitoring()
    {
        return view('pages.inventory.stockmonitoring');
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
