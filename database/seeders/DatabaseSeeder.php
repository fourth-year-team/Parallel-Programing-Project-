<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 20 customer users
        $customers = User::factory(20)->customer()->create();

        // Create 100 products
        $products = Product::factory(100)->create();

        // Create many sample orders for customers with order items
        foreach ($customers as $customer) {
            // Each customer gets 8-12 orders
            $orderCount = rand(8, 12);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()
                    ->completed()
                    ->create([
                        'user_id' => $customer->id,
                        'total_amount' => 0,
                    ]);

                // Add 3-6 items to each order
                $itemCount = rand(3, 6);
                $randomProducts = $products->random($itemCount);

                foreach ($randomProducts as $product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 5),
                        'price' => $product->price,
                    ]);
                }

                // Update order total amount after creating all items
                $order->total_amount = $order->items()->sum(\DB::raw('quantity * price'));
                $order->save();
            }
        }
    }
}
