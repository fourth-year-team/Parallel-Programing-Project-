<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
 
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


public function apiAdd(AddToCartRequest $request): JsonResponse
{
    $validated = $request->validated();
    $userId = auth()->id() ?? 7; 
    $productId = $validated['product_id'];
    $quantity = $validated['quantity'];

  
    return Redis::throttle('cart_processing')
        ->allow(50)        
        ->every(1)          
        ->then(function () use ($userId, $productId, $quantity) {
            
           
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

            
            $cartItem = DB::transaction(function () use ($userId, $productId, $quantity) {
                $item = Cart::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

                if ($item) {
                  
                    $item->increment('quantity', $quantity);
                } else {
                    $item = Cart::create([
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                    ]);
                }
                
                return $item;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Product added to cart',
                'data' => $cartItem->load('product')
            ], 201);

        }, function () {
        
            return response()->json([
                'status' => 'error',
                'message' => 'System is busy (Resource Capacity Reached). Please try again in a moment.'
            ], 429);
        });
}
   
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


    public function apiClear(): JsonResponse
    {
        auth()->user()->cartItems()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared'
        ]);
    }
}
