@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Our Products</h1>
        <p class="text-gray-600 mt-2">Browse our collection of quality products</p>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <!-- Product Image -->
                <div class="bg-gray-200 h-48 flex items-center justify-center">
                    <img src="https://via.placeholder.com/200x200" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>

                <!-- Product Info -->
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 mb-2 truncate">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>

                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                        @if ($product->stock > 0)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">In Stock ({{ $product->stock }})</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">Out of Stock</span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <a href="{{ route('products.show', $product) }}" class="block w-full bg-gray-200 text-gray-900 px-4 py-2 rounded text-center hover:bg-gray-300 transition">
                            View Details
                        </a>

                        @auth
                            @if (auth()->user()->isCustomer() && $product->stock > 0)
                                <form action="{{ route('cart.add') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-16 px-2 py-2 border border-gray-300 rounded text-center text-sm">
                                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                        Add to Cart
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded text-center hover:bg-blue-700 transition">
                                Login to Shop
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-600 text-lg">No products available at the moment.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $products->links() }}
    </div>
</div>
@endsection
