<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Get user's shopping cart (API).
     */
    public function apiIndex(): JsonResponse
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'item_count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Add product to cart (API).
     */
    public function apiAdd(AddToCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = auth()->id();
        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        // Check product exists and has sufficient stock
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        if ($product->stock < $quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock available'
            ], 422);
        }

        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart',
            'data' => $cartItem->load('product')
        ], 201);
    }

    /**
     * Update cart item quantity (API).
     */
    public function apiUpdate(Request $request, Cart $cartItem): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        if (auth()->id() !== $cartItem->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check product stock
        if ($cartItem->product->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock available'
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated',
            'data' => $cartItem->load('product')
        ]);
    }

    /**
     * Remove product from cart (API).
     */
    public function apiRemove(Cart $cartItem): JsonResponse
    {
        if (auth()->id() !== $cartItem->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product removed from cart'
        ]);
    }

    /**
     * Clear entire cart (API).
     */
    public function apiClear(): JsonResponse
    {
        auth()->user()->cartItems()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared'
        ]);
    }
}
