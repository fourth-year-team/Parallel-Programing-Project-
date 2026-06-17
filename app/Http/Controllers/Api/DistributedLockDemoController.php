<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DistributedLockDemoController extends Controller
{
    public function distributedLockCheckout(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty',
            ], 422);
        }

        // نجمع معرفات المنتجات ونرتبها حتى ما يصير deadlock إذا في أكثر من منتج
        $productIds = $cartItems->pluck('product_id')->unique()->sort()->values();
        $locks = [];

        try {
            // 1) نحاول نأخذ distributed lock لكل منتج في السلة
            foreach ($productIds as $productId) {
                $lock = Cache::lock("checkout-lock:product:{$productId}", 20);

                if (! $lock->get()) {
                    $this->releaseLocks($locks);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Another checkout is already processing this product. Please retry.',
                    ], 409);
                }

                $locks[] = $lock;
            }

            // تأخير اختياري للتجربة حتى يظهر lock بوضوح في الطلبين المتزامنين
            if ($request->boolean('demo_delay')) {
                sleep(15);
            }

            $order = DB::transaction(function () use ($user, $cartItems, $request) {
                $freshCartItems = $user->cartItems()->with('product')->get();

                if ($freshCartItems->isEmpty()) {
                    throw new \RuntimeException('Your cart became empty during checkout.');
                }

                // التحقق من المخزون أولًا
                foreach ($freshCartItems as $item) {
                    if ($item->product->stock < $item->quantity) {
                        throw new \RuntimeException('Insufficient stock for ' . $item->product->name);
                    }
                }

                $totalAmount = $freshCartItems->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'shipping_address' => $request->shipping_address,
                ]);

                foreach ($freshCartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                    ]);

                    // خصم المخزون
                    $item->product->decreaseStock($item->quantity);
                }

                // تفريغ السلة
                $user->cartItems()->delete();

                // تحديث حالة الطلب
                $order->update(['status' => 'processing']);

                return $order->load('items.product');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully with distributed lock',
                'data' => $order
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing distributed lock checkout',
            ], 500);
        } finally {
            $this->releaseLocks($locks);
        }
    }

    private function releaseLocks(array $locks): void
    {
        // نحرر الأقفال بالعكس لضمان نظافة الإغلاق
        foreach (array_reverse($locks) as $lock) {
            try {
                $lock->release();
            } catch (\Throwable $e) {
                // نتجاهل أي خطأ أثناء الإطلاق
            }
        }
    }

    public function distributedLockProbe(Request $request): JsonResponse
    {
        $lock = Cache::lock('demo:distributed-lock', 60);

        if (! $lock->get()) {
            return response()->json([
                'status' => 'error',
                'message' => 'LOCK DETECTED',
            ], 409);
        }

        try {

            sleep(20);

            return response()->json([
                'status' => 'success',
                'message' => 'Lock acquired successfully',
            ]);
        } finally {
            $lock->release();
        }
    }
}
