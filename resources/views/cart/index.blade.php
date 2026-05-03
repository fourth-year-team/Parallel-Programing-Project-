@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    @if ($cartItems->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-600 text-lg mb-4">Your cart is empty</p>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">Continue Shopping</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Product</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Price</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Quantity</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Subtotal</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-gray-900">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($cartItems as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('products.show', $item->product) }}" class="text-gray-900 hover:text-blue-600">
                                            {{ $item->product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">${{ number_format($item->product->price, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-16 px-2 py-1 border border-gray-300 rounded"
                                            onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900">${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('cart.remove', $item) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">Clear Cart</button>
                    </form>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>Calculated at checkout</span>
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between text-2xl font-bold text-gray-900">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}" class="w-full block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center font-bold">
                        Proceed to Checkout
                    </a>

                    <a href="{{ route('products.index') }}" class="w-full block mt-3 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg hover:bg-gray-300 transition text-center font-bold">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
