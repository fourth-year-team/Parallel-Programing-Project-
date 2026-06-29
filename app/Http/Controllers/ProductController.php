<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Get all products (Public API).
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);

        $cacheKey = "products:index:page:{$page}";
        $cacheStatus = 'MISS';

        $products = Cache::tags(['products:index'])->get($cacheKey);

        if (!$products) {
            $products = Product::paginate(12);

            Cache::tags(['products:index'])->put(
                $cacheKey,
                $products,
                now()->addMinutes(30)
            );
        } else {
            $cacheStatus = 'HIT';
        }

        return response()->json([
            'status' => 'success',
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ])->header('X-Cache', $cacheStatus);
    }

  
   public function apiShow(Product $product): JsonResponse
{
    $cacheKey = "products:show:{$product->id}";
    $cacheStatus = 'MISS';

    $cachedProduct = Cache::get($cacheKey);

    if (!$cachedProduct) {
        $cachedProduct = $product;

        Cache::put(
            $cacheKey,
            $cachedProduct,
            now()->addMinutes(30)
        );
    } else {
        $cacheStatus = 'HIT';
    }

    return response()->json([
        'status' => 'success',
        'data' => $cachedProduct
    ])->header('X-Cache', $cacheStatus);
}
  
    public function apiStore(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['slug'] =
            Str::slug($validated['name']) . '-' . uniqid();

        $product = Product::create($validated);

        // Clear products list cache only
        Cache::tags(['products:index'])->flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }


    public function apiUpdate(UpdateProductRequest $request, Product $product): JsonResponse
    {

        $validated = $request->validated();

        $product->update($validated);

        // Remove only this product cache
        Cache::forget("products:show:{$product->id}");

        // Clear products list cache
        Cache::tags(['products:index'])->flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function apiDestroy(Product $product): JsonResponse
    {
        // Remove this product cache first
        Cache::forget("products:show:{$product->id}");

        $product->delete();

        // Clear products list cache
        Cache::tags(['products:index'])->flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }


    public function apiAdminIndex(): JsonResponse
    {
        $products = Product::paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }
}
