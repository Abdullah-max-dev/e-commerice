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
     public function productDetail($id)
    {
        $product = Product::with(['category', 'discount', 'mainImage'])
            ->findOrFail($id);

        return response()->json([
            'product' => $this->formatProductImage($product)
        ]);
    }

    public function relatedProducts($id)
    {
        $product = Product::findOrFail($id);

        $products = Product::with(['category', 'discount', 'mainImage'])
            ->where('c_id', $product->c_id)
            ->where('p_id', '!=', $product->p_id)
            ->latest()
            ->take(4)
            ->get()
            ->map(fn ($relatedProduct) => $this->formatProductImage($relatedProduct));

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
            if ($product->relationLoaded('images')) {
            $product->gallery_images = $product->images->map(function ($image) {
                return '/uploads/products/' . $image->image;
            })->values();
        }

        return $product;
    }

}
