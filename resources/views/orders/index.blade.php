@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">My Orders</h1>

    @if ($orders->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-600 text-lg mb-4">You haven't placed any orders yet</p>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">Start Shopping</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-100 px-6 py-4 border-b">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Order #</p>
                                <p class="text-lg font-bold text-gray-900">#{{ $order->id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="text-lg font-bold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span class="inline-block px-3 py-1 rounded text-sm font-bold
                                    @if ($order->status === 'completed')
                                        bg-green-100 text-green-700
                                    @elseif ($order->status === 'processing')
                                        bg-blue-100 text-blue-700
                                    @else
                                        bg-yellow-100 text-yellow-700
                                    @endif
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <h3 class="font-bold text-gray-900 mb-3">Items</h3>
                        <div class="space-y-2">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between text-gray-600">
                                    <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                    <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-700 font-medium">View Details →</a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
