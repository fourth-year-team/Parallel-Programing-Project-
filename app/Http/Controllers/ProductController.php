<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get all products (Public API).
     */
    public function apiIndex(): JsonResponse
    {
        $products = Product::paginate(12);
        
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

    /**
     * Get a single product (Public API).
     */
    public function apiShow(Product $product): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    /**
     * Create a new product (Admin API).
     */
    public function apiStore(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update a product (Admin API).
     */
    public function apiUpdate(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();
        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Delete a product (Admin API).
     */
    public function apiDestroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get all products for admin (Admin API).
     */
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
