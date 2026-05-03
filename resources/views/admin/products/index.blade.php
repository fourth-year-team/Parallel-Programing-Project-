@extends('layouts.app')

@section('title', 'Manage Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Manage Products</h1>
        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Add New Product
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Name</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Price</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Stock</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Created</th>
                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('products.show', $product) }}" class="text-gray-900 hover:text-blue-600 font-medium">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-sm font-bold
                                @if ($product->stock > 20)
                                    bg-green-100 text-green-700
                                @elseif ($product->stock > 0)
                                    bg-yellow-100 text-yellow-700
                                @else
                                    bg-red-100 text-red-700
                                @endif
                            ">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $product->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-4 text-center text-gray-600" colspan="5">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $products->links() }}
    </div>
</div>
@endsection
