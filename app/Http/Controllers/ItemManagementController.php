<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemManagementController extends Controller
{
    public function itemmaster()
    {
        return view('pages.item.itemmaster');
    }

    public function itempublished()
    {
        return view('pages.item.itempublished');
    }

    public function downloaditem()
    {
        return view('pages.item.downloaditem');
    }
}
