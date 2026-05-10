<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadBalancerLog extends Model
{
    protected $fillable = [
        'request_uuid',
        'selected_server',
        'strategy',
        'response_time_ms',
    ];
}
