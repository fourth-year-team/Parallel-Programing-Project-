<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadBalancerState extends Model
{
    protected $fillable = [
        'current_index',
        'last_server',
    ];
}
