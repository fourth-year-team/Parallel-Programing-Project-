@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="text-4xl text-blue-600">💰</div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</p>
                </div>
                <div class="text-4xl text-green-600">📦</div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Products</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
                </div>
                <div class="text-4xl text-purple-600">🛍️</div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Customers</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCustomers }}</p>
                </div>
                <div class="text-4xl text-orange-600">👥</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Monthly Sales</h2>
            <canvas id="salesChart" height="80"></canvas>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.products.index') }}" class="block bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center font-bold">
                    Manage Products
                </a>
                <a href="{{ route('admin.products.create') }}" class="block bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 text-center font-bold">
                    Add New Product
                </a>
                <a href="{{ route('products.index') }}" class="block bg-gray-600 text-white px-4 py-3 rounded-lg hover:bg-gray-700 text-center font-bold">
                    View Store
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-100 px-6 py-4">
            <h2 class="text-2xl font-bold text-gray-900">Recent Orders</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Order ID</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Customer</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-900">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($recentOrders as $order)
                    <tr>
                        <td class="px-6 py-4 font-bold text-gray-900">#{{ $order->id }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded text-sm font-bold bg-green-100 text-green-700">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-4 text-center text-gray-600" colspan="5">No orders yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Monthly Sales',
                data: {!! json_encode($salesData) !!},
                borderColor: 'rgb(37, 99, 235)',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointBackgroundColor: 'rgb(37, 99, 235)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
