<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\ProductImage;
use App\Models\Discount;

class ProductController extends Controller
{
    // Add product
    public function saveProduct(Request $request)
    {
        $request->validate([
            'c_id'          => 'required|exists:categories,c_id',
            'p_name'        => 'required|string|max:255',
            'p_price'       => 'required|numeric',
            'p_stock'       => 'required|integer',
            'p_description' => 'required|string',
            'images'        => 'required|array|min:1',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::create([
            'v_id'          => auth()->id(),
            'c_id'          => $request->c_id,
            'p_name'        => $request->p_name,
            'p_price'       => $request->p_price,
            'p_stock'       => $request->p_stock,
            'p_description' => $request->p_description,
        ]);

        // Save images and set first image as main
        foreach ($request->file('images') as $index => $image) {
            $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $name);

            ProductImage::create([
                'p_id'    => $product->p_id,
                'image'   => $name,
                'is_main' => $index === 0 ? 1 : 0
            ]);
        }

        return response()->json([
            'success' => true,
            'product' => $this->addFinalPrice($product->load(['category', 'images', 'mainImage','discount']))
        ], 201);
    }

    // Show all products for vendor
    public function showProduct()
    {
        $products = Product::with(['category', 'images', 'mainImage','discount'])
            ->where('v_id', auth()->id())
            ->get()
            ->map(fn($p) => $this->addFinalPrice($p));

        return response()->json([
            'products' => $products
        ]);
    }

    // Show single product
    public function show($id)
    {
        $product = Product::with(['category','images','mainImage','discount'])
            ->where('p_id', $id)
            ->where('v_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'product' => $this->addFinalPrice($product)
        ]);
    }

    // Add/update discount
    public function storeDiscount(Request $req, $p_id)
    {
        $req->validate([
            'type'      => 'required|in:percentage,fixed',
            'value'     => 'required|numeric|min:1',
            'starts_at' => 'nullable|date',
            'ends_at'   => 'nullable|date|after_or_equal:starts_at',
        ]);

        $product = Product::with('discount', 'mainImage')->findOrFail($p_id);

        $discount = $product->discount;
        if ($discount) {
            $discount->update([
                'type'  => $req->type,
                'value' => $req->value,
                'starts_at'      => $req->starts_at,
                'ends_at'        => $req->ends_at
            ]);
        } else {
            $discount = Discount::create([
                'p_id'           => $product->p_id,
                'v_id'           => auth()->id(),
                'type'           => $req->type,
                'value'          => $req->value,
                'starts_at'      => $req->starts_at,
                'ends_at'        => $req->ends_at
            ]);
        }

        $product->load('discount', 'mainImage');
        Product::where('p_id',$product->p_id)
                ->update(['is_top_deal'=> 1]);


        return response()->json([
            'success' => true,
            'product' => $this->addFinalPrice($product)
        ]);
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::with('images')->where('p_id', $id)->firstOrFail();

        // delete all product images
        foreach ($product->images as $img) {
            $path = public_path('uploads/products/' . $img->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $img->delete();
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::with('images')
            ->where('p_id', $id)
            ->where('v_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'c_id'          => 'sometimes|exists:categories,c_id',
            'p_name'        => 'sometimes|string|max:255',
            'p_price'       => 'sometimes|numeric',
            'p_stock'       => 'sometimes|integer',
            'p_description' => 'sometimes|string',
            'is_top_deal'   => 'sometimes|boolean',

            'images'        => 'sometimes|array',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'remove_images' => 'sometimes|array',
            'remove_images.*' => 'integer',
        ]);

        $product->update($validated);

        // Remove images
        if ($request->filled('remove_images')) {
            foreach ($request->remove_images as $imgId) {
                $img = ProductImage::where('id', $imgId)
                    ->where('p_id', $product->p_id)
                    ->first();

                if ($img) {
                    $path = public_path('uploads/products/' . $img->image);
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $img->delete();
                }
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $name);

                ProductImage::create([
                    'p_id'    => $product->p_id,
                    'image'   => $name,
                    'is_main' => 0 // new images are not main
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'product' => $this->addFinalPrice($product->load(['category', 'images', 'mainImage']))
        ]);
    }

    // Helper to calculate final price & main image
    private function addFinalPrice($product)
    {
        // Main image
        $product->p_image = $product->mainImage ? '/uploads/products/' . $product->mainImage->image : '/default-product.png';

        // Discount
        if ($product->discount) {
            if ($product->discount->type === 'percentage') {
                $product->final_price = $product->p_price - ($product->p_price * $product->discount->value / 100);
            } else { // fixed
                $product->final_price = $product->p_price - $product->discount->value;
            }
        } else {
            $product->final_price = $product->p_price;
        }

        return $product;
    }
}
