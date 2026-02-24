<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use App\Models\VenderNotification;

class HomeController extends Controller
{

    public function popularProducts()
    {
        $products = Product::with(['category', 'discount', 'mainImage','images','vender' ])
            ->whereHas('category', function ($q) {
                $q->where('is_popular', 1);
            })
            ->where('is_active', true)
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
        $product = Product::with(['category', 'discount', 'mainImage','images','vender' ])
            ->where('is_active', true)
            ->findOrFail($id);
        $averageRating = ProductComment::where('product_id', $product->p_id)

            ->whereNull('parent_id')
            ->avg('rating');

        return response()->json([
            'product' => $this->formatProductImage($product),
            'average_rating' => $averageRating ? round((float) $averageRating, 1) : 0,
            'ratings_count' => ProductComment::where('product_id', $product->p_id)->whereNull('parent_id')->count(),
        ]);
    }

    public function relatedProducts($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);

        $baseQuery = Product::with(['category', 'discount', 'mainImage', 'images', 'vender'])
            ->where('is_active', true)
            ->where('p_id', '!=', $product->p_id)
            ->where('is_active', true)
            ->latest();

        $products = (clone $baseQuery)
            ->when($product->c_id, fn ($query) => $query->where('c_id', $product->c_id))
            ->take(4)
            ->get();

        if ($products->count() < 4) {
            $needed = 4 - $products->count();

            $fallbackProducts = (clone $baseQuery)
                ->whereNotIn('p_id', $products->pluck('p_id')->all())
                ->take($needed)
                ->get();

            $products = $products->concat($fallbackProducts);
        }

        $products = $products
            ->take(4)
            ->map(fn ($relatedProduct) => $this->formatProductImage($relatedProduct))
            ->values();



        return response()->json([
            'products' => $products
        ]);
    }

    public function topDeals()
    {
        $products = Product::with(['category', 'discount', 'mainImage','images','vender' ])
            ->where('is_top_deal', 1)
            ->where('is_active', true)
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


        $product->gallery_images = $product->images->map(function ($image) {
            return '/uploads/products/' . $image->image;
        })->values();

        if ($product->relationLoaded('vender') && $product->vender) {
            $product->vender->shop_logo_url = $product->vender->shop_logo
                ? '/storage/shop_logos/' . $product->vender->shop_logo
                : null;
        }

        return $product;
    }


}
