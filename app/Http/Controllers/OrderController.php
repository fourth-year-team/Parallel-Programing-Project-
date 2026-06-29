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

    //Optimistic
    public function apiCheckoutOptimistic(CheckoutRequest $request): JsonResponse
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
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
            ]);

            $totalAmount = 0;

            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient stock for ' . $product->name
                    ], 422);
                }

                $updated = Product::where('id', $product->id)
                    ->where('lock_version', $product->lock_version)
                    ->where('stock', '>=', $item->quantity)
                    ->update([
                        'stock' => $product->stock - $item->quantity,
                        'lock_version' => $product->lock_version + 1,
                        'updated_at' => now(),
                    ]);

                if ($updated === 0) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Stock changed by another request, please retry'
                    ], 409);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ]);

                $totalAmount += $product->price * $item->quantity;
            }

            $order->update([
                'total_amount' => $totalAmount,
                'status' => 'processing',
            ]);

            $user->cartItems()->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully with optimistic locking',
                'data' => $order->load('items.product')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your order'
            ], 500);
        }
    }
}
