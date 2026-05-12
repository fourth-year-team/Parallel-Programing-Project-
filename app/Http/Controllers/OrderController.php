<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\Product; 
class OrderController extends Controller
{
    /**
     * Get user's orders (API).
     */
    public function apiIndex(): JsonResponse
    {
        $orders = auth()->user()->orders()->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    /**
     * Get order details (API).
     */
    public function apiShow(Order $order): JsonResponse
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order->load('items.product')
        ]);
    }

    /**
     * Process checkout and create an order (API).
     */
  public function apiCheckout(CheckoutRequest $request): JsonResponse
    {
        $user = auth()->user();
        
       
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty'
            ], 422);
        }

        try {
           
            return DB::transaction(function () use ($request, $user, $cartItems) {
                
                $totalAmount = 0;
                $orderItemsData = [];

                foreach ($cartItems as $item) {
                  
                    $product = Product::where('id', $item->product_id)
                        ->lockForUpdate() 
                        ->first();

                
                    if (!$product || $product->stock < $item->quantity) {
                        throw new Exception("Insufficient stock for {$product->name}");
                    }

                    
                    $subtotal = $product->price * $item->quantity;
                    $totalAmount += $subtotal;

                  
                    $product->decrement('stock', $item->quantity);

                    $orderItemsData[] = [
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $product->price,
                    ];
                }

                $order = Order::create([
                    'user_id'          => $user->id,
                    'total_amount'     => $totalAmount,
                    'status'           => 'processing',
                    'shipping_address' => $request->shipping_address,
                ]);

                
                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

              
                $user->cartItems()->delete();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Order placed successfully and stock secured',
                    'data'    => $order->load('items.product')
                ], 201);
            });

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get all orders (Admin API).
     */
    public function apiAdminIndex(): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $orders = Order::with('user', 'items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    /**
     * Update order status (Admin API).
     */
    public function apiUpdateStatus(Request $request, Order $order): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $newStatus = $request->status;

        // Handle stock restoration if cancelling
        if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {
            foreach ($order->items as $item) {
                $item->product->increaseStock($item->quantity);
            }
        }

        $order->update(['status' => $newStatus]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated',
            'data' => $order
        ]);
    }
}
