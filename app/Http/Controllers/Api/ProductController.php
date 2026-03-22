<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index ()
    {
        $products = Product::with('category')->where('is_available', true)->get();

        return response()->json($products);
    }

    public function product_details ($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();

        return response()->json($product);
    }
}