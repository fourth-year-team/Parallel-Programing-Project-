<?php

namespace App\Services;

use App\Models\LoadBalancerLog;
use App\Models\LoadBalancerState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoadBalancerService
{
    private array $servers = [
        'api-node-1',
        'api-node-2',
        'api-node-3',
    ];

    public function routeNext(): array
    {
        return DB::transaction(function () {
            $state = LoadBalancerState::query()->lockForUpdate()->first();

            if (!$state) {
                $state = LoadBalancerState::create([
                    'current_index' => 0,
                    'last_server' => null,
                ]);

                $state = LoadBalancerState::query()->lockForUpdate()->first();
            }

            $index = $state->current_index % count($this->servers);
            $server = $this->servers[$index];
            $nextIndex = ($index + 1) % count($this->servers);

            $state->current_index = $nextIndex;
            $state->last_server = $server;
            $state->save();

            $log = LoadBalancerLog::create([
                'request_uuid' => (string) Str::uuid(),
                'selected_server' => $server,
                'strategy' => 'round_robin',
                'response_time_ms' => null,
            ]);

            return [
                'log_id' => $log->id,
                'server' => $server,
                'strategy' => 'round_robin',
                'request_uuid' => $log->request_uuid,
            ];
        });
    }
}
