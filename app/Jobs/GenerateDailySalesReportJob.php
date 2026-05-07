<?php

namespace App\Jobs;

use App\Models\DailySalesReport;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDailySalesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public string $reportDate)
    {
    }

    public function handle(): void
    {
        $ordersCount = 0;
        $itemsCount = 0;
        $totalSales = 0.0;

        Order::query()
            ->whereDate('created_at', $this->reportDate)
            ->where('status', 'completed')
            ->with('items')
            ->orderBy('id')
            ->chunkById(100, function ($orders) use (&$ordersCount, &$itemsCount, &$totalSales) {
                foreach ($orders as $order) {
                    $ordersCount++;

                    foreach ($order->items as $item) {
                        $itemsCount++;
                        $totalSales += ((float) $item->price) * $item->quantity;
                    }
                }
            });

        DailySalesReport::updateOrCreate(
            ['report_date' => $this->reportDate],
            [
                'orders_count' => $ordersCount,
                'items_count' => $itemsCount,
                'total_sales' => $totalSales,
                'generated_at' => now(),
            ]
        );

        Log::info("Daily sales report generated for {$this->reportDate}");
    }
}
