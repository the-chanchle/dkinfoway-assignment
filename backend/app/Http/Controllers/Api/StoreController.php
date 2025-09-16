<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Store;

class StoreController extends Controller
{
    public function index()
    {
        return response()->json(Store::orderBy('name')->get());
    }
}