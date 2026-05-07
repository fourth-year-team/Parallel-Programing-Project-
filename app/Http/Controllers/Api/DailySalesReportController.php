<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDailySalesReportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailySalesReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $data = $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = $data['date'] ?? now()->toDateString();

        GenerateDailySalesReportJob::dispatch($date);

        return response()->json([
            'status' => 'success',
            'message' => 'Daily sales report queued successfully',
            'report_date' => $date,
        ], 202);
    }
}
