<footer class="bg-gray-900 text-gray-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-white font-bold mb-4">About E-Shop</h3>
                <p class="text-sm">A modern e-commerce platform built with Laravel and Tailwind CSS, offering seamless shopping experience.</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-bold mb-4">Quick Links</h3>
                <ul class="text-sm space-y-2">
                    <li><a href="{{ route('products.index') }}" class="hover:text-white">Products</a></li>
                    @auth
                        @if (auth()->user()->isCustomer())
                            <li><a href="{{ route('cart.index') }}" class="hover:text-white">Cart</a></li>
                            <li><a href="{{ route('orders.index') }}" class="hover:text-white">Orders</a></li>
                        @endif
                    @endauth
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-white font-bold mb-4">Support</h3>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white">FAQ</a></li>
                    <li><a href="#" class="hover:text-white">Shipping Info</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-white font-bold mb-4">Legal</h3>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white">Return Policy</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm">
            <p>&copy; 2024 E-Shop. All rights reserved.</p>
        </div>
    </div>
</footer>
