<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function popularProducts()
    {
        $products = Product::with(['category', 'discount'])
            ->whereHas('category', function ($q) {
                $q->where('is_popular', 1);
            })
            ->latest()
            ->take(8)
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }

    public function topDeals()
    {
        $products = Product::with(['category', 'discount'])
            ->where('is_top_deal', 1)
            ->latest()
            ->take(8)
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }

}
