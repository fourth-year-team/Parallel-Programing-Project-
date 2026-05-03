@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div>
            <div class="bg-gray-200 rounded-lg overflow-hidden h-96">
                <img src="https://via.placeholder.com/400x400" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
        </div>

        <!-- Product Details -->
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <div class="flex items-center justify-between mb-6">
                <span class="text-4xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                @if ($product->stock > 0)
                    <span class="text-lg bg-green-100 text-green-700 px-4 py-2 rounded">In Stock ({{ $product->stock }} available)</span>
                @else
                    <span class="text-lg bg-red-100 text-red-700 px-4 py-2 rounded">Out of Stock</span>
                @endif
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Description</h3>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $product->description }}</p>
            </div>

            @auth
                @if (auth()->user()->isCustomer() && $product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-24 px-4 py-2 border border-gray-300 rounded">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-lg font-bold">
                            Add to Cart
                        </button>
                    </form>
                @endif
            @else
                <div class="mb-6">
                    <a href="{{ route('login') }}" class="w-full block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-lg font-bold text-center">
                        Login to Add to Cart
                    </a>
                </div>
            @endauth

            <div class="border-t pt-6">
                <p class="text-gray-600 text-sm">
                    <strong>SKU:</strong> {{ $product->slug }}
                </p>
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div class="mt-12">
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">← Back to Products</a>
    </div>
</div>
@endsection
