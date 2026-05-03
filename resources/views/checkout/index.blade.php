@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Checkout</h1>

    <form action="{{ route('orders.checkout') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Shipping Address</h2>

            <div>
                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea id="shipping_address" name="shipping_address" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('shipping_address') border-red-500 @enderror" placeholder="Enter your complete shipping address">{{ old('shipping_address') }}</textarea>
                @error('shipping_address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>

            <div class="space-y-4 mb-6">
                @php
                    $total = 0;
                    $cartItems = auth()->user()->cartItems()->with('product')->get();
                @endphp

                @foreach ($cartItems as $item)
                    @php
                        $subtotal = $item->product->price * $item->quantity;
                        $total += $subtotal;
                    @endphp
                    <div class="flex justify-between text-gray-700">
                        <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between text-2xl font-bold text-gray-900 mb-6">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-lg font-bold">
                    Place Order
                </button>

                <a href="{{ route('cart.index') }}" class="w-full block mt-3 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg hover:bg-gray-300 transition text-center font-bold">
                    Back to Cart
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
