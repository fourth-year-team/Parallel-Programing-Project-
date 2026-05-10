<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoadBalancerLog;
use App\Models\LoadBalancerState;
use App\Services\LoadBalancerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LoadDistributionController extends Controller
{
    public function simulate(Request $request, LoadBalancerService $service): JsonResponse
    {
        $start = microtime(true);

        $assignment = $service->routeNext();

        usleep(random_int(20, 60) * 1000);

        $responseTimeMs = (int) round((microtime(true) - $start) * 1000);

        LoadBalancerLog::whereKey($assignment['log_id'])->update([
            'response_time_ms' => $responseTimeMs,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request routed successfully',
            'data' => [
                'selected_server' => $assignment['server'],
                'strategy' => $assignment['strategy'],
                'request_uuid' => $assignment['request_uuid'],
                'response_time_ms' => $responseTimeMs,
            ],
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = LoadBalancerLog::query()
            ->select('selected_server', DB::raw('COUNT(*) as requests_count'))
            ->groupBy('selected_server')
            ->orderBy('selected_server')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    public function reset(): JsonResponse
    {
        LoadBalancerLog::truncate();

        $state = LoadBalancerState::query()->first();

        if (!$state) {
            LoadBalancerState::create([
                'current_index' => 0,
                'last_server' => null,
            ]);
        } else {
            $state->update([
                'current_index' => 0,
                'last_server' => null,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Load balancer state has been reset',
        ]);
    }
}
