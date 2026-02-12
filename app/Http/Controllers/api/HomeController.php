<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function popularProducts()
    {
        $products = Product::with(['category', 'discount', 'mainImage'])
            ->whereHas('category', function ($q) {
                $q->where('is_popular', 1);
            })
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($product) => $this->formatProductImage($product));

        return response()->json([
            'products' => $products
        ]);
    }

    public function topDeals()
    {
        $products = Product::with(['category', 'discount','mainImage'])
            ->where('is_top_deal', 1)
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($product) => $this->formatProductImage($product));

        return response()->json([
            'products' => $products
        ]);
    }
      private function formatProductImage($product)
    {
        $product->p_image = $product->mainImage
            ? '/uploads/products/' . $product->mainImage->image
            : '/default-product.png';

        return $product;
    }

}
