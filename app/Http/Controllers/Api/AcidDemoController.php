<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcidDemoController extends Controller
{
    public function naiveCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
            'force_fail' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty'
            ], 422);
        }

        try {
            $totalAmount = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($cartItems as $index => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decreaseStock($item->quantity);

                // Intentionally failing after the first element until a partial commit effect appears
                if ($request->boolean('force_fail') && $index === 0) {
                    throw new \RuntimeException('Forced failure for ACID demo (naive)');
                }
            }

            $user->cartItems()->delete();
            $order->update(['status' => 'processing']);

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully without transaction',
                'data' => $order->load('items.product'),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed without transaction',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    public function transactionCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
            'force_fail' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty'
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($user, $cartItems, $validated, $request) {
                $totalAmount = $cartItems->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'shipping_address' => $validated['shipping_address'],
                ]);

                foreach ($cartItems as $index => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                    ]);

                    $item->product->decreaseStock($item->quantity);

                    // Deliberately failing after the first item until the rollback test is complete
                    if ($request->boolean('force_fail') && $index === 0) {
                        throw new \RuntimeException('Forced failure for ACID demo (transaction)');
                    }
                }

                $user->cartItems()->delete();
                $order->update(['status' => 'processing']);

                return $order->load('items.product');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully with transaction',
                'data' => $order,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed with transaction',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }
}
