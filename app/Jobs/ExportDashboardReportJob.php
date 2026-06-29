<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExportDashboardReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
{
    try {
        $orders = \App\Models\Order::with('user')->latest()->limit(10)->get();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_amount');

        $data = [
            'title' => 'Emergency Test',
            'date' => date('d/m/Y'),
            'orders' => $orders,
            'total' => $totalRevenue
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sales', $data);
        
       
        $outputPath = public_path('final_test_report.pdf');
        
        $pdf->save($outputPath);

        if (file_exists($outputPath)) {
            \Illuminate\Support\Facades\Log::info("SUCCESS! File found at: " . $outputPath);
        } else {
            \Illuminate\Support\Facades\Log::error("CRITICAL: Code finished but file NOT found on disk.");
        }
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("JOB FAILED: " . $e->getMessage());
    }
}
}