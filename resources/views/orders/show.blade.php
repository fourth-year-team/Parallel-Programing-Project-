@extends('layouts.app')

@section('title', 'Order ' . $order->id)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700">← Back to Orders</a>
    </div>

    <h1 class="text-4xl font-bold text-gray-900 mb-8">Order #{{ $order->id }}</h1>

    <!-- Order Status -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600">Order Date</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <span class="inline-block px-3 py-1 rounded text-sm font-bold
                    @if ($order->status === 'completed')
                        bg-green-100 text-green-700
                    @elseif ($order->status === 'processing')
                        bg-blue-100 text-blue-700
                    @elseif ($order->status === 'cancelled')
                        bg-red-100 text-red-700
                    @else
                        bg-yellow-100 text-yellow-700
                    @endif
                ">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Amount</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Items</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->items->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 px-6 py-4">
            <h2 class="text-xl font-bold text-gray-900">Order Items</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Quantity</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('products.show', $item->product) }}" class="text-gray-900 hover:text-blue-600">
                                {{ $item->product->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-600">${{ number_format($item->price, 2) }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-gray-50 px-6 py-4 border-t">
            <div class="text-right">
                <p class="text-gray-600">Total: <span class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</span></p>
            </div>
        </div>
    </div>

    <!-- Shipping Address -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Address</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $order->shipping_address }}</p>
    </div>
</div>
@endsection
