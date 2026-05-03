@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Edit Product</h1>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
        @csrf
        @method('PATCH')

        <!-- Product Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
            <textarea id="description" name="description" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('description') border-red-500 @enderror" required>{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Price -->
        <div class="mb-6">
            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price ($) *</label>
            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('price') border-red-500 @enderror" required>
            @error('price')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Stock -->
        <div class="mb-6">
            <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('stock') border-red-500 @enderror" required>
            @error('stock')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Path -->
        <div class="mb-8">
            <label for="image_path" class="block text-sm font-medium text-gray-700 mb-2">Image Path</label>
            <input type="text" id="image_path" name="image_path" value="{{ old('image_path', $product->image_path) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('image_path') border-red-500 @enderror" placeholder="products/image.jpg">
            @error('image_path')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-bold">
                Update Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="flex-1 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg hover:bg-gray-300 font-bold text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
