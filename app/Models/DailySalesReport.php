<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySalesReport extends Model
{
     protected $fillable = [
        'report_date',
        'orders_count',
        'items_count',
        'total_sales',
        'generated_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_sales' => 'decimal:2',
        'generated_at' => 'datetime',
    ];
}
