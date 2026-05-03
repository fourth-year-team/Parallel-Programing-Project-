@php
    $cartCount = auth()->check() ? auth()->user()->cartItems()->count() : 0;
@endphp

<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">E-Shop</span>
                </a>
            </div>

            <!-- Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-blue-600">Products</a>

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                        <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-blue-600">Manage Products</a>
                    @else
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-blue-600">
                            Cart
                            @if ($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-blue-600">Orders</a>
                    @endif
                @endauth
            </div>

            <!-- Auth Links -->
            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-blue-600">{{ auth()->user()->name }}</button>
                        <div class="hidden group-hover:block absolute right-0 w-48 bg-white shadow-lg rounded-md">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
