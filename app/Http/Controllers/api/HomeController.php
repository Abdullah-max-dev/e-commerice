<?php

namespace App\Http\Controllers\Api;
use App\Models\Category;
use App\Models\Product;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function popularProducts()
    {
        $products = Product::with('category')
            ->whereHas('category', function ($q) {
                $q->where('is_popular', 1);
            })
            ->latest()
            ->take(8)
            ->get();

        return response()->json([
            'products' => $products
        ]);
        dd($products);
    }



}
