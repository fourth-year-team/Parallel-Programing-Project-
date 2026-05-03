<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        ]);

        // Create 5 customer users
        $customers = User::factory(5)->customer()->create();

        // Create 100 products
        $products = Product::factory(100)->create();

        // Create sample orders for customers with order items
        foreach ($customers as $customer) {
            // Each customer gets 2-4 orders
            $orderCount = rand(2, 4);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()
                    ->completed()
                    ->create(['user_id' => $customer->id]);

                // Add 2-5 items to each order
                $itemCount = rand(2, 5);
                $randomProducts = $products->random($itemCount);

                foreach ($randomProducts as $product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 3),
                        'price' => $product->price,
                    ]);

                    // Update order total amount
                    $order->total_amount = $order->items()->sum(\DB::raw('quantity * price'));
                    $order->save();
                }
            }
        }
    }
}
